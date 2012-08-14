

CMS.Form.Field = $.inherit(
    {
        id: "",
        type: "",
        path: "",
        label: "",
        parent: null,
        uid:'',
        value: null,
        fields: null,
        required: false,

        __constructor: function (xml, parent) {
            this.type = xml.nodeName;

            this.id = new String($(xml).attr("id"));
            this.label = $(xml).attr("label") ? $(xml).attr("label") : this.id.ucWord();
            this.required = $(xml).attr("required") ? $(xml).attr("required") == "true" : false;
            this.uid = $(xml).attr("uid");
            this.namespace = $(xml).attr("namespace");
            if (this.uid == undefined || this.uid == '' || this.uid == null)
            {
                // TODO - add check to see if this uid is infact unique
                this.uid = guidGenerator();
            }

            this.parent = parent;
            if (parent.path) {
                this.path = parent.path + "[" + this.id + "]";
                this.elPath = parent.elPath + "_" + this.id;
            } else
                this.elPath = this.path = this.id;

        },

        render: function () {
            return '<div class="row">' +this.renderLabel() + this.renderField() + '</div>';
        },

        renderDone: function () {
        },

        renderLabel: function () {
            return '<div class="label"><label>' + this.label + '</label></div>';
        },

        renderField: function () {
            return '<div class="field"><input type="hidden" name="' + this.path + '" id="' + this.elPath + '" /></div>';
        },

        debug: function (indent) {
            return "\t".repeat(indent) + "Field " + this.type + ", id:" + this.id + ", path:" + this.elPath + ", value:" + this.value + "\n";
        },

        populate: function (xml)
        {
            this.uid = $(xml).attr("uid");
            if (this.uid == undefined || this.uid == '' || this.uid == null)
            {
                // TODO - add check to see if this uid is infact unique
                this.uid = guidGenerator();
            }
        }
		
		,setValue : function (value) 
		{
        	this.value = value;
			$('#'+this.elPath).val(this.value);			
		}
			
		, getValue : function ()
		{
            if (this.value)
			    return CMS.Form.stripInvalidXmlChars (this.value);
            else
                return '';
		}

        ,isValid: function () {
            // get value from form
            this.value = $('#' + this.elPath).val();

            // validate
            this.valid = true;

            // validate children (if we have them)
            var valid = this.valid;
            if (this.fields) {
                jQuery.each(this.fields, function (id) {
                    if (!this.isValid())
                        valid = false;
                });
            }

            return valid;
        }

		, toXml: function (xmlDoc) 
		{
		    var el = xmlDoc.createElement(this.type);
		    el.setAttribute("id", this.id);
		    el.setAttribute("uid", this.uid);
            el.setAttribute("namespace", this.namespace);

		    if (this.value) {
		        var newCDATA = xmlDoc.createCDATASection(this.getValue());
		        el.appendChild(newCDATA);
		    }

		    if (this.fields) {
		        jQuery.each(this.fields, function (id) {
		            el.appendChild(this.toXml(xmlDoc));
		        });
		    }


		    return el;
		}	
    });
