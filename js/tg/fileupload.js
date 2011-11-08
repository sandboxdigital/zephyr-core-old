/*
 * jQuery FileUploadWrapper
 *
 * Copyright (c) 2006, 2007, 2008 Thomas Garrood
 *
 * Depends:
 *
 *	swfupload.js
 *
 */


(function ($) { // hide the namespace
    if (window.Tg == undefined) {
        alert("Tg namespace not defiend - have you included core.js?");
    }

    Tg.FileUtils = {}
    Tg.FileUtils.getExtension = function (filename) {
        //c(filename);
        var ext = /^.+\.([^.]+)$/.exec(filename);
        return ext == null ? "" : new String(ext[1]).toLowerCase();
    }

    var defaults = {
        uploadUrl: "/file/upload",
        shimName: "swfShim",
        swfUrl: "/core/flash/swfupload.swf",
        debug: false,
        sizeLimit: "2 MB", //The file_size_limit parameter will accept a unit. Valid units are B, KB, MB, and GB. The default unit is KB.
        fileTypes: "*.jpg;*.gif;*.png;*.flv;*.f4v;*.mp3",
        mode: 'single'   // single | multiple
    };
    
    Tg.FileUploadAbstract = Class.extend({
    	init: function (options)
        {
    		this._options = $.extend({}, defaults, options);
            this._name = this._options.name;
            if (this._options.id)
                this._id = this._options.id;
            else
                this._id = this._name;
            this._upload = $(this._options.upload);
            this._cancel = $(this._options.cancel);
            this._preview = $(this._options.preview);
            this._progress = $(this._options.progress);
            this._callback = this._options.callback;

            this._cancel.bind('click', $.proxy(this.onCancelClick, this));
        }
    	,onCancelClick: function () {
            //console.info ("onCancelClick");
            this.cancelUpload();
            this._upload.show();
            this._cancel.hide();
        },
        
        cancelUpload : function ()
        {
        	
        },

        debug: function (ex) {
            //c(ex);
        },

        onUploadStarted: function (file) {
            this._upload.hide();
            this._cancel.show();
        },

        onUploadProgress: function (percent) {
            this._progress.text(percent + "%");
        },

        onUploadSuccess: function (file) {
            this._progress
			.text("Transfer complete")
		    .animate({ opacity: 1.0 }, 1500)
		    .fadeOut('slow');
	
	        this.addFileRow (file);
	
	        this._upload.attr('value', 'Change file');

            this._upload.show();
            this._cancel.hide();
            
            if (this._options["onSuccess"])
            	this._options["onSuccess"].call (this, jsonObject);
        },
        
        onUploadFail : function (data)
        {
            alert("Error During Upload\n\nThere was a problem uploading the file.\n\nServer Returned:\n" + data);
            this._upload.show();
            this._cancel.hide();
            return false;
        },

        cancel: function (id) {
            $(id + '_btnUpload').show();
            $(id + '_btnCancel').hide();

        }
        
        ,addFileRow : function (jsonObject) 
        { 
        	if (this._options.mode == 'single') {
	            this._preview.empty();
	        }
	
        	var url = jsonObject.url;        	
        	if (jsonObject.thumbnailUrl != undefined)
        	{        		
        		url = jsonObject.thumbnailUrl;
        	}
            var ext = Tg.FileUtils.getExtension(url);

            if (ext != "jpg" && ext != "jpeg" && ext != "png" && ext != "gif")
                url = "/core/images/fileicons/"+ext+".png";
            
        	var xhtml = '<tr>';
            xhtml += '<td class="fileUploadThumbnail"><img id="' + this._id + '_image" src="' + url + '" width="32" height="32"  /></td>';
            xhtml += '<td id="' + this._id + '_name" class="fileUploadName">' + jsonObject.name + '</td>';
            xhtml += '<td class="fileUploadDelete">';
            xhtml += '<a href="#" class="formFileUploadDelete">Remove this file</a>';
            if (this._options.mode == 'single') 
            	xhtml += '<input type="hidden" id="' + this._id + '" name="' + this._name + '" value="' + jsonObject.id + '" />';
            else 
            	xhtml += '<input type="hidden" id="' + this._id + '" name="' + this._name + '[]" value="' + jsonObject.id + '" />';
            xhtml += '</td>';
            xhtml += '</tr>';

	        this._preview.append(xhtml);
            
	        this._preview.find('.formFileUploadDelete').bind('click', function () {
                if (confirm ('Are you sure you want to remove this file?'))
            	{
                	$(this).parent().find('input').val('0');
                	$(this).parent().parent().fadeOut ();            	
            	}
                return false;
            });
        }
    });

    Tg.FileUploadValums = Tg.FileUploadAbstract.extend({
    	init: function(options){
    		this._super(options);
    		
    		this._upload.wrap ('<div id="'+this._id+'_valumes" />')
    		
    		var exts = this._options.fileTypes.replace(/\*\./g, '');
    		
    		this.button = new qq.FileUploaderBasic ({
    			button:$('#'+this._id+'_valumes')[0],
    			debug:true,
   		        action: '/file/upload-valums',
   		        allowedExtensions:exts.split(';'),
	            onSubmit: $.proxy(this.onSubmit, this),
	            onProgress: $.proxy(this.onProgress, this),
	            onComplete: $.proxy(this.onComplete, this),
	            onCancel: $.proxy(this.onCancel, this),
    		});
    	},
    	
	    onSubmit: function(id, fileName)
	    {
    		this.onUploadStarted();
    	},
	    
    	onProgress: function(id, fileName, loaded, total)
	    {
            var percent = Math.ceil((loaded / total) * 100);
            this.onUploadProgress(percent)
	    },
	    
	    onComplete: function(id, fileName, responseJSON)
	    {
	    	if (responseJSON.success)
	    		this.onUploadSuccess (responseJSON.file);
	    	else
	    		this.onUploadFail (responseJSON);	    		
	    },
	    
	    onCancel: function(id, fileName)
	    {
    		this.onCancelClick ();
	    }
    });

    Tg.FileUploadSWFUpload = Tg.FileUploadAbstract.extend({
    	init: function(options){
    		this._super(options);
    	        
	        this._upload.wrap('<div style="position:relative;"></div>');
	        this._upload.after('<div style="position:absolute;top:0px;left:0px"><div id="' + this._options.shimName + '"></div></div>');

	        this._width = this._upload.outerWidth();
	        this._height = this._upload.outerHeight();
	        
	        this._previousId = null;
	        this._settings = {
	            flash_url: this._options.swfUrl,
	            upload_url: this._options.uploadUrl,
	            file_upload_limit: 10,
	            file_queue_limit: 0,
	            file_size_limit: this._options.sizeLimit,
	            file_types: this._options.fileTypes,
	            debug: this._options.debug,
	            post_params: {
	                "versionId": "-"
	            },
	            use_query_string: false,

	            // Button Settings
	            button_placeholder_id: "swfShim",
	            button_width: this._width,
	            button_height: this._height,
	            button_window_mode: SWFUpload.WINDOW_MODE.TRANSPARENT,
	            button_cursor: SWFUpload.CURSOR.HAND,

	            // The event handler functions are defined in handlers.js
	            file_queued_handler: $.proxy(this.fileQueued, this),
	            file_queue_error_handler: $.proxy(this.fileQueueError, this),
	            file_dialog_complete_handler: $.proxy(this.fileDialogComplete, this),
	            upload_start_handler: $.proxy(this.uploadStart, this),
	            upload_progress_handler: $.proxy(this.uploadProgress, this),
	            upload_error_handler: $.proxy(this.uploadError, this),
	            upload_success_handler: $.proxy(this.uploadSuccess, this),
	            upload_complete_handler: $.proxy(this.uploadComplete, this)
	        };
	        
	        this._swfupload = new SWFUpload(this._settings);
    	}

    	,cancelUpload: function () {
            this._swfupload.cancelUpload();
        },

        fileQueued: function (file) {
            $('#' + this._swfupload.movieName).css("height", "0px");
            this.onUploadStarted();
        },

        fileQueueError: function (file, errorCode, message) {
            if (errorCode == -110){
            	alert (message+"\nLimit is "+this._settings.file_size_limit);
            }
        },

        fileDialogComplete: function (numFilesSelected, numFilesQueued) {
            this._swfupload.startUpload();
        },

        uploadStart: function (file) {
        },

        uploadProgress: function (file, bytesLoaded, bytesTotal) {
            var percent = Math.ceil((bytesLoaded / bytesTotal) * 100);
            this.onUploadProgress(percent)
        },

        uploadSuccess: function (file, serverData) {
            $('#' + this._swfupload.movieName).css("height", this._height);
            
            var jsonObject = {};
            try {
                jsonObject = eval(serverData);
            }
            catch (err) {
                try {
                    jsonObject = parseMSJSONString(serverData);
                } catch (err) {
                	 this.onUploadFail(serverData);
                    return false;
                }
            }

            this.onUploadSuccess(jsonObject);
            return true;
        },

        uploadError: function (file, errorCode, message) {
        },

        uploadComplete: function (file) {
        },

        selectFiles: function (id) {
            //c(id)
            this._previousId = id;
            this._swfupload.selectFiles();
        }
    });

    function FileUploadWrapper() {
        this.init()
    }

    $.extend(FileUploadWrapper.prototype, {
        init: function () {
            this._Fields = new Array();
        },

        addField: function (options) {
        	var count = this._Fields.length;
        	if (jQuery.browser.webkit || jQuery.browser.mozilla)
        		this._Fields[count] = new Tg.FileUploadValums(options);
        	else
        		this._Fields[count] = new Tg.FileUploadSWFUpload(options);          
            return this._Fields[count];
        }
    });

    $.swfupload = new FileUploadWrapper();

})(jQuery);