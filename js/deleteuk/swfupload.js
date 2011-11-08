/*
 * jQuery SWFUploadWrapper
 *
 * Copyright (c) 2006, 2007, 2008 Thomas Garrood
 *
 * Depends:
 *
 *	swfupload.js
 *
 */

(function($) { // hide the namespace
	var defaults = {
		uploadUrl:"/file/upload",
		shimName:"swfShim",
		swfUrl:"/core/flash/swfupload.swf",
		debug:false,
		sizeLimit:"32000",
		fileTypes : "*.jpg;*.gif;*.png;*.flv;*.f4v;*.mp3;",
		mode:'single'   // single | multiple
	};
		
		
	function SWFUploadField (options) {
		this.init (options)
	}
	
	$.extend(SWFUploadField.prototype, {
		init : function (options) {
			options = $.extend(defaults, options);
			this._options = options;
			this._name = options.name;
			this._upload = $(options.upload);
			this._cancel = $(options.cancel);
			this._preview = $(options.preview);
			this._progress = $(options.progress);
			this._callback = options.callback;
			
			this._upload.wrap ('<div style="position:relative;"></div>');
			this._upload.after ('<div style="position:absolute;top:0px;left:0px;"><div id="'+options.shimName+'"></div></div>');
			
			//TODO - add check for button width/height
			var button_width=this._upload.width();
			var button_height=this._upload.height();
			
			this._cancel.bind ('click', this.onCancelClick.scope(this));
	
			this._previousId = null;
			this._settings = {
				flash_url : options.swfUrl,
				upload_url: options.uploadUrl,
				file_upload_limit : 10,
				file_queue_limit : 0,
				file_size_limit : options.sizeLimit,
				file_types : options.fileTypes,
				debug: options.debug,
				post_params : {
					"versionId" : "-"
					},
				use_query_string : false,
				
				// Button Settings
				button_placeholder_id : "swfShim",
				button_width: this._upload.width(),
				button_height: this._upload.height(),
				button_window_mode: SWFUpload.WINDOW_MODE.TRANSPARENT,
				button_cursor: SWFUpload.CURSOR.HAND,
	
				// The event handler functions are defined in handlers.js
				file_queued_handler : this.fileQueued.scope (this),
				file_queue_error_handler : this.fileQueueError.scope (this),
				file_dialog_complete_handler : this.fileDialogComplete.scope (this),
				upload_start_handler : this.uploadStart.scope (this),
				upload_progress_handler : this.uploadProgress.scope (this),
				upload_error_handler : this.uploadError.scope (this),
				upload_success_handler : this.uploadSuccess.scope (this),
				upload_complete_handler : this.uploadComplete.scope (this)
			};
	
			this._swfupload = new SWFUpload(this._settings);
		},
	
		onCancelClick: function () {
			//console.info ("onCancelClick");
			this._swfupload.cancelUpload ();
			this._upload.show ();
			this._cancel.hide ();
		},
	
		debug : function (ex) {
			//c(ex);
		},
	
		fileQueued : function (file) {		
			$('#'+this._swfupload.movieName).css("height","0px");
			this._upload.hide ();
			this._cancel.show ();
		},
	
		fileQueueError : function (file, errorCode, message) {
			//c("fileQueueError");
		},
	
		fileDialogComplete : function (numFilesSelected, numFilesQueued) {
			//c("fileDialogComplete");
			this._swfupload.startUpload ();
		},
	
		uploadStart : function (file) {
			//c("uploadStart");
			//console.info ("uploadStart");
		},
	
		uploadProgress : function (file, bytesLoaded, bytesTotal) {
			var percent = Math.ceil((bytesLoaded / bytesTotal) * 100);
			c ("uploadProgress :"+percent+"%");
			this._progress.text (percent+"%");
		},
	
		uploadSuccess : function (file, serverData) {
			c(serverData);
			try {
				var jsonObject = eval(serverData);
			}
			catch(err) {
				alert("Error During Upload\n\nThere was a problem uploading the file.\n\nServer Returned:\n"+serverData);
				$('#'+this._swfupload.movieName).css("height","20px");
				this._upload.show ();
				this._cancel.hide ();
				return false;
			}
	
			if(typeof(window[this._callback]) == 'function') {
				window[this._callback](jsonObject, this._options);
			} else
				this.uploadSucccesCallBack (jsonObject, this._options);
			
			$('#'+this._swfupload.movieName).css("height","20px");
			this._upload.show ();
			this._cancel.hide ();
		},
		
		uploadSucccesCallBack : function (jsonObject, options) {
			this._progress
				.text ("Transfer complete")
			    .animate({opacity: 1.0}, 1500)
			    .fadeOut('slow');;

			if (this._options.mode == 'single') {
				this._preview.empty ();
			}
			
			var xhtml = '<tr><td><img src="'+jsonObject.url+'"  /></td>';
			xhtml += '<td>'+jsonObject.name+'</td>';
			xhtml += '<td> <a href="#" class="formFileUploadDelete">Remove this file</a> ';
			xhtml += '<input type="checkbox" id="'+this._name+'_delete" name="'+this._name+'_delete" value="true" />';
			xhtml += '<input type="hidden" id="'+this._name+'" name="'+this._name+'" value="'+jsonObject.id+'" />';
			xhtml += '</td>';
			xhtml += '</tr>';
			
			this._preview.append(xhtml);
			
			this._upload.attr('value', 'Change file');
		},
	
		uploadError : function (file, errorCode, message) {
			//c("uploadError");
			//console.info ("uploadError");
			//c("file: "+file);
			//c("errorCode: "+errorCode);
			//c("message: "+message);
		},
	
		uploadComplete : function (file) {
			//c("uploadComplete");
			//console.info ("uploadComplete");
			//c("file: ");
			//c(file);
		},
	
		selectFiles : function (id) {
			//c(id)
			this._previousId = id;
			this._swfupload.selectFiles ();
		},
	
		cancel : function (id) {
			$(id+'_btnUpload').show ();
			$(id+'_btnCancel').hide ();
	
		}
	});
	
	function SWFUploadWrapper () {
		this.init ()
	}
	
	$.extend(SWFUploadWrapper.prototype, {
		init : function () {
			this._Fields = new Array ();
		},
	
		addField : function (options) {
			this._Fields[this._Fields.length] = new SWFUploadField (options);
		}
	});
	
	$.swfupload = new SWFUploadWrapper ();
	alert ('/core/deleteuk/swfupload.js is depreciated. Use /core/tg/fileupload.js');

})(jQuery);