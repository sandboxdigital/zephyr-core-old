// THIS MOVED TO /core/js/tinymce.js

/*if (window.tinyMceOptions == undefined) {
	var tinyMceOptions = {
        mode : "none",
        theme: "advanced",
        plugins: "media,advimage,paste,inlinepopups",
        dialog_type : "modal",
        theme_advanced_buttons1: "bold,italic,underline,bullist,numlist,separator,outdent,indent,|,link,unlink,image,|,cleanup,removeformat,|,pastetext,pasteword,|,code",
        theme_advanced_buttons2: "justifyleft,justifycenter,justifyright,|,formatselect,",
        theme_advanced_buttons3: "",
        theme_advanced_toolbar_location: "top",
        theme_advanced_statusbar_location: "bottom",
        theme_advanced_toolbar_align: "left",
        theme_advanced_resizing: true,
        theme_advanced_resize_horizontal: false,
        file_browser_callback: "myFileBrowser"
        //, document_base_url: "../../.." // relative to this file cms.form.js
       ,relative_urls : false
    };
}

tinyMCE.init(tinyMceOptions);
*/

var CMS = {}
CMS.FormDefaults = {
    container: "#formContainer",
    url: '/admin/content/save',
    path: 'cmsForm',
    subform: false
}

CMS.Form = function (options) {
    this.init(options);
}

$.extend(CMS.Form.prototype, {
    formXml: null,
    container: null,
    fields: null,
    id: "",
    path: "",
    elPath: "",
    type: "form",
    options: null,
    name: '',
    input: null,

    init: function (options) {
        this.options = $.extend({}, CMS.FormDefaults, options);

        this.container = $(this.options['container']);

        this.path = this.options['path'];
        this.elPath = this.options['path'];

        if (this.options['subform']) {
            this.input = $('input#' + this.options['name'] + '');
            if (this.input) {
                var form = this.input.parents('form');
                if (form.length == 0)
                    alert('Subform set and form not found');
                else
                    $(form).submit($.inScope(this.onSubmit, this));
            } else
                alert('Subform set and input not found');
        }
    },

    setOptions: function (options) {
        //c("!");
        //c(this.options);
        this.options = $.extend(this.options, options);
    },

    loadFile: function (url) {
        $.ajax({
            type: "GET",
            url: url,
            dataType: "xml",
            success: $.inScope(this.handleLoad, this)
        });
    },

    clear: function () {
        Tg.TinyMce.unregisterAll ();

        // unload all fields
        this.fields = new Array();

        this.render();
        this.renderDone();
    },

    loadXml: function (xmlString) {
        Tg.TinyMce.unregisterAll ();

        // unload all fields
        this.fields = new Array();

        xmlString = unescape(xmlString);
        //xmlString = urldecode(xmlString);
        this.handleLoad(CMS.Form.parseXml(xmlString));
    },

    handleLoad: function (xml) {
        this.formXml = $(xml).children()[0];

        this.multilingual = $(this.formXml).attr('multilingual')=='true'?true:false;
        
        if (this.multilingual)
        {
        	this.languages = $(this.formXml).attr('languages').split(',');
        	var self = this; 
        	
        	var block = $(self.formXml).find(' > block#lang').detach();
        	
        	if (block.length < 0)
        		alert ('Multilingual form must have a single block with id=lang');
        	
            $(this.languages).each(function (key) {
            	var lang = this;
            	var newBlock = $(block).clone().attr('id',lang);
            	$(self.formXml).append(newBlock);
            });
            
            c (this.formXml)
        }
        
    	CMS.Form.loadFields(this.formXml, this);

        this.render();
        this.renderDone();
    },

    populateXml: function (xmlString) {
        //c("!2");
        xmlString = unescape(xmlString);
        //xmlString = urldecode(xmlString);
        
        this.handlePopulate(CMS.Form.parseXml(xmlString));
    },

    handlePopulate: function (xml) {
        var xml = $(xml).children()[0];
        var _this = this;

        $(xml).children().each(function (key) {
            var id = $(this).attr("id");
            if (_this.fields[id])
                _this.fields[id].populate(this);
        })
    }

    , render: function () {
        var output = '';
        if (!this.options['subform'])
            output += '<form class="cmsFormForm" method="post">';

        output += '<div class="cmsForm">';

        jQuery.each(this.fields, function (id) {
            output += this.render();
        });

        c(this.options)
        
        if (this.options['subform'] == false && this.options['hideSave'] == false)
            output += '<div><input type="button" id="cmsFormSave" value="Save" /> <input type="button" id="cmsFormSaveClose" value="Save &amp; Close" /></div>';

        output += '</div>';

        if (!this.options['subform'])
            output += '</form>';

        this.container.html(output);

        var _this = this;
        $('#cmsFormSave').click($.inScope(this.save, this));
        $('#cmsFormSaveClose').click($.inScope(this.saveAndClose, this));
    }

    , save: function () {
        this.isValid();
        var d = this.debug();
        var xml = this.toXml();
        this.saveAjax(xml, this.onSave);
    }

    , saveAndClose: function () {
        this.isValid();
        var xml = this.toXml();
        this.saveAjax(xml, this.onSaveClose);
    }

    , saveAjax: function (xml, onSave) { 
        if (xml === false || xml == null || xml.trim() == "")
        	alert ('XML not found.\n\nPlease send the content you are trying to upload to your site administrator');
        else {
	        var data = {
	            'pageId': this.options['pageId'],
	            'contentId': this.options['contentId'],
	            'contentVersion': this.options['contentVersion'],
	            'cmsForm': xml
	        };
	
	        $('#cmsFormSave').hide();
	        $('#cmsFormSaveClose').hide();
	
	        $.ajax({
	            url: this.options['url']
	            , type: 'POST'
	            , dataType: 'json'
		        //, dataType: 'script'
	            , data: data
	            , success: jQuery.inScope(onSave, this)
	        });
        }
    }

    , renderDone: function () {
        jQuery.each(this.fields, function (id) {
            this.renderDone();
        });
    }

    , isValid: function () {
        tinyMCE.triggerSave();
        jQuery.each(this.fields, function (id) {
            var valid = this.isValid();
        });

        return true;
    }

    , toXml: function () {
        var xmlDoc = CMS.Form.createXml('data');
        var el = xmlDoc.childNodes[0];
        try {
            if (this.fields) {
                jQuery.each(this.fields, function (id) {
                    el.appendChild(this.toXml(xmlDoc));
                });
            }
            //			c(xmlDoc)
            return CMS.Form.xmlToString(xmlDoc);
        } catch (e) {
            alert (e.message);
            return false;
        }
    }

    , debug: function (containerId) {
        var output = "";

        jQuery.each(this.fields, function (id) {
            output += this.debug(0);
        });

        if (containerId != null)
        	$(containerId).html("<pre>" + output + "</pre>");
        
        return output;
    },

    onSave: function (response) {
        $('#cmsFormSave').show();

        if (this.options["onSave"])
            this.options["onSave"](response);
    },

    onSaveClose: function () {
        document.location.reload();
    },

    onSubmit: function () {
        try {
            var valid = this.isValid();

            if (valid) {
                var xml = this.toXml();

                this.input.val(xml);

                return true;
            } else
                return false;
        } catch (e) {
            c(e)

            return false;
        }
    }
});

CMS.Form.Textareas = new Array();

CMS.Form.parseXml = function (xmlString) 
{
    if(typeof(ActiveXObject) != 'undefined')
	{
		//Internet Explorer
        xmlDoc = new ActiveXObject("Microsoft.XMLDOM");
        xmlDoc.async = "false";
        xmlDoc.loadXML(xmlString);
        return xmlDoc;
    } else {
    	try {
	        var parser = new DOMParser();
	        xmlDoc = parser.parseFromString(xmlString, "text/xml");
    	} catch (e)
    	{
    		alert (e);
    	}
    }
    return xmlDoc;
}



/**
* Create a new Document object. If no arguments are specified,
* the document will be empty. If a root tag is specified, the document
* will contain that single root tag. If the root tag has a namespace
* prefix, the second argument must specify the URL that identifies the
* namespace.
*/
CMS.Form.createXml = function (rootTagName, namespaceURL) {
    if (!rootTagName) rootTagName = "";
    if (!namespaceURL) namespaceURL = "";
    if (document.implementation && document.implementation.createDocument) {
        // This is the W3C standard way to do it
        return document.implementation.createDocument(namespaceURL, rootTagName, null);
    }
    else { // This is the IE way to do it
        // Create an empty document as an ActiveX object
        // If there is no root element, this is all we have to do
        var doc = new ActiveXObject("MSXML2.DOMDocument");
        // If there is a root tag, initialize the document
        if (rootTagName) {
            // Look for a namespace prefix
            var prefix = "";
            var tagname = rootTagName;
            var p = rootTagName.indexOf(':');
            if (p != -1) {
                prefix = rootTagName.substring(0, p);
                tagname = rootTagName.substring(p + 1);
            }
            // If we have a namespace, we must have a namespace prefix
            // If we don't have a namespace, we discard any prefix
            if (namespaceURL) {
                if (!prefix) prefix = "a0"; // What Firefox uses
            }
            else prefix = "";
            // Create the root element (with optional namespace) as a
            // string of text
            var text = "<" + (prefix ? (prefix + ":") : "") + tagname +
          (namespaceURL
           ? (" xmlns:" + prefix + '="' + namespaceURL + '"')
           : "") +
          "/>";
            // And parse that text into the empty document
            doc.loadXML(text);
        }
        return doc;
    }
};

CMS.Form.xmlToString = function (elem) {

    var serialized;

    if(typeof(XMLSerializer) != 'undefined') {
	    // XMLSerializer exists in current Mozilla browsers    
		try {
		    serializer = new XMLSerializer();
		    serialized = serializer.serializeToString(elem);
		}
		catch (e) {
		    // Internet Explorer has a different approach to serializing XML
		   alert ("Oh dear unable to serialize XML\nError:\n"+e);
		}
    } else {
        // Internet Explorer has a different approach to serializing XML
        serialized = elem.xml;
    }

    return serialized;
}

CMS.Form.loadFields = function (xml, parent, settings) {
    parent.fields = new Object();

    $(xml).children().each(function (key) {
        var field = CMS.Form.loadField(this, parent, settings);
        if (field)
            parent.fields[field.id] = field;
    })
}

CMS.Form.loadField = function (xml, parent, settings) {    
    var settings = settings || {};
    var field = null;
    try {
        var evals = "new CMS.Form.Field." + xml.nodeName.ucWord() + "(xml, parent, settings);";
        field = eval(evals);
    } catch (e) {
        alert('Unknown field ' + xml.nodeName + ', try including cms.form.field.' + xml.nodeName + '.js');
    }

    return field;
}


function urldecode(str) {
    // http://kevin.vanzonneveld.net
    // +   original by: Philip Peterson
    // +   improved by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
    // +      input by: AJ
    // +   improved by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
    // +   improved by: Brett Zamir (http://brett-zamir.me)
    // +      input by: travc
    // +      input by: Brett Zamir (http://brett-zamir.me)
    // +   bugfixed by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
    // +   improved by: Lars Fischer
    // +      input by: Ratheous
    // %          note 1: info on what encoding functions to use from: http://xkr.us/articles/javascript/encode-compare/
    // *     example 1: urldecode('Kevin+van+Zonneveld%21');
    // *     returns 1: 'Kevin van Zonneveld!'
    // *     example 2: urldecode('http%3A%2F%2Fkevin.vanzonneveld.net%2F');
    // *     returns 2: 'http://kevin.vanzonneveld.net/'
    // *     example 3: urldecode('http%3A%2F%2Fwww.google.nl%2Fsearch%3Fq%3Dphp.js%26ie%3Dutf-8%26oe%3Dutf-8%26aq%3Dt%26rls%3Dcom.ubuntu%3Aen-US%3Aunofficial%26client%3Dfirefox-a');
    // *     returns 3: 'http://www.google.nl/search?q=php.js&ie=utf-8&oe=utf-8&aq=t&rls=com.ubuntu:en-US:unofficial&client=firefox-a'

    //    var hash_map = {}, ret = str.toString(), unicodeStr='', hexEscStr='';
    //    
    //    var replacer = function(search, replace, str) {
    //        var tmp_arr = [];
    //        tmp_arr = str.split(search);
    //        return tmp_arr.join(replace);
    //    };
    //    
    //    // The hash_map is identical to the one in urlencode.
    //    hash_map["'"]   = '%27';
    //    hash_map['(']   = '%28';
    //    hash_map[')']   = '%29';
    //    hash_map['*']   = '%2A';
    //    hash_map['~']   = '%7E';
    //    hash_map['!']   = '%21';
    //    hash_map['%20'] = '+';
    //    hash_map['\u00DC'] = '%DC';
    //    hash_map['\u00FC'] = '%FC';
    //    hash_map['\u00C4'] = '%D4';
    //    hash_map['\u00E4'] = '%E4';
    //    hash_map['\u00D6'] = '%D6';
    //    hash_map['\u00F6'] = '%F6';
    //    hash_map['\u00DF'] = '%DF';
    //    hash_map['\u20AC'] = '%80';
    //    hash_map['\u0081'] = '%81';
    //    hash_map['\u201A'] = '%82';
    //    hash_map['\u0192'] = '%83';
    //    hash_map['\u201E'] = '%84';
    //    hash_map['\u2026'] = '%85';
    //    hash_map['\u2020'] = '%86';
    //    hash_map['\u2021'] = '%87';
    //    hash_map['\u02C6'] = '%88';
    //    hash_map['\u2030'] = '%89';
    //    hash_map['\u0160'] = '%8A';
    //    hash_map['\u2039'] = '%8B';
    //    hash_map['\u0152'] = '%8C';
    //    hash_map['\u008D'] = '%8D';
    //    hash_map['\u017D'] = '%8E';
    //    hash_map['\u008F'] = '%8F';
    //    hash_map['\u0090'] = '%90';
    //    hash_map['\u2018'] = '%91';
    //    hash_map['\u2019'] = '%92';
    //    hash_map['\u201C'] = '%93';
    //    hash_map['\u201D'] = '%94';
    //    hash_map['\u2022'] = '%95';
    //    hash_map['\u2013'] = '%96';
    //    hash_map['\u2014'] = '%97';
    //    hash_map['\u02DC'] = '%98';
    //    hash_map['\u2122'] = '%99';
    //    hash_map['\u0161'] = '%9A';
    //    hash_map['\u203A'] = '%9B';
    //    hash_map['\u0153'] = '%9C';
    //    hash_map['\u009D'] = '%9D';
    //    hash_map['\u017E'] = '%9E';
    //    hash_map['\u0178'] = '%9F';
    // 
    //    for (unicodeStr in hash_map) {
    //        hexEscStr = hash_map[unicodeStr]; // Switch order when decoding
    //        ret = replacer(hexEscStr, unicodeStr, ret); // Custom replace. No regexing
    //    }
    //    
    //    c (ret);
    //    
    //    decodeURIComponent('%EF');
    //    c (ret);
    //    return ret;

    // End with decodeURIComponent, which most resembles PHP's encoding functions
    //    ret = decodeURIComponent(ret);
    return decodeURIComponent(str.replace(/\+/g, '%20'));
}

CMS.Form.stripInvalidXmlChars = function (s)
{
	//var s = "London by nightA fantastic nocturnal tour Le Monde.fr : Actualité à la Une!@#$%^&*()?";
	// 
	// removes http://www.w3.org/TR/2000/REC-xml-20001006#NT-Char
	// Char	   ::=   	#x9 | #xA | #xD | [#x20-#xD7FF] | [#xE000-#xFFFD] | [#x10000-#x10FFFF]	/* any Unicode character, excluding the surrogate blocks, FFFE, and FFFF. */
	// JS unicode range only goes as high as \uFFFF
	// Uses negative lookahead
	var re = /(?![\u0009\u000A\u000D\u0020-\uD7FF]|[\uE000-\uFFFD])./g;
	s = s.replace(re, "");
	return s;
}


