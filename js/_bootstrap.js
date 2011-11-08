
var files = new Array();

function require (filename, filetype) {
	if (files.indexOf(filename) == -1) {
		 if (filetype == "css") { // if filename is an external CSS file
		     var fileref = document.createElement("link");
			fileref.setAttribute("rel", "stylesheet");
			fileref.setAttribute("type", "text/css");
			fileref.setAttribute("href", filename);
		} else {
			// default to js
			var fileref = document.createElement('script')
			fileref.setAttribute("type", "text/javascript");
			fileref.setAttribute("src", filename);
		}

		if (typeof fileref != "undefined")
		    document.getElementsByTagName("head")[0].appendChild(fileref);       
        files.push(filename);
	}
}

require ('/core/js/tg/core.js')
require ('/core/js/jquery.inherit.js');
require ('/core/js/jquery.contextmenu.r2.js');
require ('/core/js/swfupload/swfupload.js');
require ('/core/js/tg/fileupload.js');
require ('/core/js/tg/cms.form.js');
require ('/core/js/tiny_mce/jquery.tinymce.js');