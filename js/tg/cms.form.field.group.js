


CMS.Form.Field.Group = $.inherit(
	CMS.Form.Field,
	{
	    options: null,
	    fields: null,
	    numFields: 0,
	    maxFields: 1000000,

	    __constructor: function (xml, parent) {
	        this.__base(xml, parent);

	        var options = new Object();
	        var _this = this;

	        this.maxFields = $(xml).attr("max") ? $(xml).attr("max") : 1000000;

	        $(xml).children().each(function (key) {
	            var option = CMS.Form.loadField(this, _this);
	            options[option.id] = option;
	        });
	        this.options = options;
	        this.fields = new Array();
	    },

	    debug: function (indent) {
	        var output = "	".repeat(indent) + "Field " + this.type + ", id:" + this.id + ", path:" + this.path + "\n" + "	".repeat(indent) + "{\n";
	        jQuery.each(this.options, function (id) {
	            output += this.debug(indent + 1);
	        })
	        output += "	".repeat(indent) + "}\n";
	        return output;
	    },

	    populate: function (xml) {
            this.__base(xml);
	        var _this = this;
	        $(xml).children().each(function (key) {
	            var field = _this.addOption($(this).attr("id"), true, this);
//	            field.populate(this);
	        });
	    },

	    render: function () {
	        var output = "";
	        jQuery.each(this.fields, function (id) {
	            output += this.render();
	        })

	        var html = '<div class="CMSGroup CMSGroup'+this.id+'" style="-webkit-user-select: none;-khtml-user-select: none;-moz-user-select: none;-o-user-select: none;user-select: none;"><div class="CMSGroupTitle" id="' + this.elPath + '-title"><div class="left">' + this.label + '</div>';
	        html += '<a href="#" class="CMSGroupAdd CMSIconAdd">Add</a>';
//	        html += '<a href="#" class="CMSGroupPaste CMSIconPaste">Paste</a>';
//	        html += '<a href="#" class="CMSGroupCopy CMSIconCopy">Copy</a>';
	        html += '</div>';
	        html += '<div class="CMSGroupBody" id="' + this.elPath + '">' + output + '</div></div>';
	        
	        return html;
	    },

	    renderDone: function () {
	        var bindings = new Object();
	        var html = '<div id="CMSAddMenu' + this.elPath + '"><ul>';
	        for (key in this.options) {
	            bindings['cms_add_' + key] = $.inScope(this.onMenuClick, this);
	            html += '<li id="cms_add_' + key + '">' + this.options[key].label + '</li>';
	        }
	        html += '</ul></div>';

	        $(html)
            .hide()
            .css({ position: 'absolute', zIndex: '500' })
            .appendTo('body');

	        $("#" + this.elPath + '-title a.CMSGroupAdd').contextMenu("CMSAddMenu" + this.elPath, {
	            "bindings": bindings,
	            "menuPosition": "object",
	            "event": "click",
	            "menuAlign": "right",
	            onShowMenu: $.inScope(this.onShowMenu, this)
	        });


	        $("#" + this.elPath + '-title a.CMSGroupCopy').click(function () {
	        
	        });
	        
	        $("#" + this.elPath + '-title a.CMSGroupPaste').click(function () {
	        
	        });
	        

	        $("#" + this.elPath).sortable({
	            "axis": "y",
	            "handle": ".CMSElementDetailsMove",
	            "forcePlaceholderSize": true,
	            "placeholder": ".CMSElementPlaceholder",
	            "containment": "parent",
	            "start": $.inScope(this.onDragStart, this)
	            ,"stop": $.inScope(this.onDragUpdate, this)
	        });

	        jQuery.each(this.fields, function (id) {
	            this.renderDone();
	        });
	    },

	    addOption: function (id, expanded, contentXml) {
	        var field = CMS.Form.loadField(this.options[id].xml, this);
	        this.fields[this.fields.length] = field;
	        this.numFields++;

	        $('#' + this.elPath).append(field.render());
	        field.renderDone ();
	        field.populate (contentXml);

	        if (this.fields.length >= this.maxFields) {
	            $("#" + this.elPath + '-title a.CMSGroupAdd').hide();
	        }

	        return field;
	    },

	    deleteOption: function (elPath) {
	        for (var i = 0; i < this.fields.length; i++) {
	            if (this.fields[i].elPath == elPath) {
	                $('#' + elPath).remove();
	                this.fields.splice(i, 1);
	                i = 1000000;
	            }
	        }

	        if (this.fields.length < this.maxFields) {
	            $("#" + this.elPath + '-title a.CMSGroupAdd').show();
	        }
	    },

	    onShowMenu: function (ev, menu) {
	        return menu; // lazy loading - could create the menu now!!!
	    },

	    onMenuClick: function (trigger, target) {
	        this.addOption(target.id.substr(8), true)
	    }

		, onDragStart: function () {
		    for (var i = 0; i < CMS.Form.Textareas.length; i++) {
		        CMS.Form.Textareas[i].removeControl ();
		    }
		}

		, onDragStop: function () {
			c ("stop");
		}

		, onDragUpdate: function () {
			c ("update");
			
		    var newFields = new Array();
		    var ids = $("#" + this.elPath).sortable('toArray');
		    for (var j = 0; j < ids.length; j++) {
		        for (var i = 0; i < this.fields.length; i++) {
		            if (this.fields[i].elPath == ids[j]) {
		                newFields.push(this.fields[i]);
		                i = 1000000;
		            }
		        }
		    }
		    this.fields = newFields;

		    for (var i = 0; i < CMS.Form.Textareas.length; i++) {
		        CMS.Form.Textareas[i].addControl ();
		    }
		}
	});



