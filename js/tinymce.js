if (window.tinyMceOptions == undefined) {
	var tinyMceOptions = {
        mode : "none",
        theme: "advanced",
        plugins: "media,advimage,paste,inlinepopups",
        dialog_type : "modal",
        theme_advanced_buttons1: "bold,italic,underline,bullist,numlist,separator,outdent,indent,|,link,unlink,image,|,code",
        theme_advanced_buttons2: "justifyleft,justifycenter,justifyright,|,formatselect,|,cleanup,removeformat,|,pastetext,pasteword",
        theme_advanced_buttons3: "",
        theme_advanced_toolbar_location: "top",
        theme_advanced_statusbar_location: "bottom",
        theme_advanced_toolbar_align: "left",
        theme_advanced_resizing: true,
        theme_advanced_resize_horizontal: false,
        file_browser_callback: "myFileBrowser"
        //, document_base_url: "../../.." // relative to this file cms.form.js
       ,relative_urls : false
       ,theme_advanced_resizing_use_cookie : false
    };
}

tinyMCE.init(tinyMceOptions);

Tg.TinyMce = {};

$.extend (Tg.TinyMce, {
	textareaIds : []

	,register : function (id)
	{
		this.textareaIds.push (id);
		tinyMCE.execCommand("mceAddControl", false, id);
	}

	,unregisterAll : function ()
	{
	    tinyMCE.triggerSave();
		for (var i = 0; i < this.textareaIds.length; i++) {
		    tinyMCE.execCommand('mceRemoveControl', false, this.textareaIds[i]);
		}
		
		this.textareaIds = [];
	}
});

function myFileBrowser (field_name, url, type, win) 
{
	c (type);
	
	if (type == 'image') {
		var config = {
			'onSelect':function (file) {
			 	if (Tg.FileManager.fileBrowserWindow)
					Tg.FileManager.fileBrowserWindow.hide ();
			 	
				 c (file);
				 
				 var url = file.url;
		        // insert information now
		        win.document.getElementById(field_name).value = url;

		        // are we an image browser
		        if (typeof(win.ImageDialog) != "undefined")
		        {
		            // we are, so update image dimensions and preview if necessary
		            if (win.ImageDialog.getImageData) win.ImageDialog.getImageData();
		            if (win.ImageDialog.showPreviewImage) win.ImageDialog.showPreviewImage(url);
		        }
		    }
		}
		Tg.FileManager.showBrowserInWindow (config);
	}

    return false;
}
