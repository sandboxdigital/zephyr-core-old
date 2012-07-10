// singleton class for managing pages
// extends Ext.util.Observable

Tg.Config.PageFactory = {
    urlAddPage: "/admin/site-page-save"
    , urlEditPage: "/admin/site-page-save"
    , urlDeletePage: "/admin/site-page-delete"
    , urlMovePage: "/admin/site-page-move"
}

Tg.PageFactory = Ext.extend(Ext.util.Observable, {
    name: 'barfoo'
	, templates: null
	, themes: null
	, pages: null
	, pageTree: null

	, constructor: function () {
	    //alert ("hi")
	}

	, setPages: function (pages) {
	    this.pages = pages;
	}

	, setTemplates: function (a) {
	    this.templates = a;
	}

	, setThemes: function (a) {
	    this.themes = a;
	}

	, findTemplate: function (templateId) {
	    for (var i = 0; i < this.templates.length; i++) {
	        if (this.templates[i]["id"] == templateId)
	            return this.templates[i];
	    }
	    return false;
	}

    , findPath: function (pageId) {
        var path = this._findPath (this.pages, pageId)
        return path;
    }

    , _findPath : function (node, pageId) {
        if (node['id'] == pageId)
            return "/";

        for (var i = 0; i < node['pages'].length; i++) {
            var returnNode = this._findPath(node['pages'][i], pageId);
            if (returnNode != null)
            {
                return "/"+node['pages'][i].name+returnNode;
            }
        }

        return null;
    }

	, findPage: function (pageId) {
	    return this._findPage(this.pages, pageId)
	}

	, _findPage: function (node, pageId) {
	    if (node['id'] == pageId)
	        return node;

	    for (var i = 0; i < node['pages'].length; i++) {
	        var returnNode = this._findPage(node['pages'][i], pageId);
	        if (returnNode != null)
	            return returnNode;
	    }

	    return null;
	}

	, _getForm: function () {
	    var templates = new Array();
	    Ext.each(this.templates, function (el) {
	        templates.push(new Array(el.id, el.name));
	    });

	    var themes = new Array();
	    Ext.each(this.themes, function (el) {
	        themes.push(new Array(el.id, el.name));
	    });

	    return new Ext.FormPanel({
	        labelAlign: 'right',
	        labelWidth: 85,
	        width: 400,
	        waitMsgTarget: true,
	        defaultType: 'textfield',
	        items: [
				new Ext.form.Hidden({
				    name: 'id'
				}),
				new Ext.form.Hidden({
				    name: 'parentId'
				}),
		        {
		            fieldLabel: 'Title',
		            name: 'title',
		            width: 290
		        },
				{
				    fieldLabel: 'Path',
				    name: 'name',
				    width: 290
				},

				new Ext.form.ComboBox({
				    fieldLabel: 'Template',
				    name: 'templateId_test',
				    allowBlank: false,
				    triggerAction: 'all',
				    lazyRender: true,
				    typeAhead: false,
				    mode: 'local',
				    forceSelection: true,
				    store: templates,
				    hiddenName: 'templateId',
				    width: 290
				}),

				new Ext.form.ComboBox({
				    fieldLabel: 'Theme',
				    name: 'themeId_test',
				    allowBlank: false,
				    triggerAction: 'all',
				    lazyRender: true,
				    typeAhead: false,
				    mode: 'local',
				    forceSelection: true,
				    store: themes,
				    hiddenName: 'themeId',
				    width: 290
				}),

				new Ext.form.ComboBox({
				    fieldLabel: 'Show in nav',
				    name: 'visible_test',
				    allowBlank: false,
				    triggerAction: 'all',
				    lazyRender: true,
				    typeAhead: false,
				    mode: 'local',
				    forceSelection: true,
				    store: [[1, "Yes"], [0, "No"]],
				    hiddenName: 'visible',
				    width: 290
				})
			]
	    });
	}

	, addPage: function (parentId) {
	    var page = this.findPage(parentId);
	    var template = this.findTemplate (page.templateId);
	    var defaultSubPageTemplate = page.templateId;
	    
	    if (template.defaultSubPageTemplate && template.defaultSubPageTemplate > 0)
	    {
	    	defaultSubPageTemplate = template.defaultSubPageTemplate;
	    }
	    	
	    var form = this._getForm();

	    var pageClone = {
	        'id': 0
			, 'templateId': defaultSubPageTemplate
			, 'themeId': page.themeId
			, 'parentId': parentId
			, 'visible': 1
	    };

	    form.form.setValues(pageClone);
	    
	    var win = new Tg.FormWindow(form);
	    win.url = Tg.Config.PageFactory.urlAddPage;
	    win.show();
	    win.focus();
	    win.on('save', function (formPanel, values, results) {
	        Tg.PageFactory.pageTree.getNodeById(parentId).appendChild(new Tg.Page(results.page));
	        page.pages.push(results.page);
	    });
	}

	, editPage: function (pageId) {
	    var page = this.findPage(pageId);
	    var form = this._getForm();
	    form.form.setValues(page);

	    var win = new Tg.FormWindow(form);
	    win.url = Tg.Config.PageFactory.urlEditPage;
	    win.show();
	    win.focus();
	    win.on('save', function (formPanel, values, results) {
        	// TODO: move to a page.update() method
	        var node = Tg.PageFactory.pageTree.getNodeById(pageId);
	        node.setText(values.title);

	        for (var property in values) {
	            Tg.PageFactory.pageTree.getNodeById(pageId).attributes[property] = values[property];

	            page[property] = values[property];
	        }
	    });
	}

    , editPageSave: function (form) {
         // TODO - update the page json model
         form.submit({
            clientValidation: true,
            url: Tg.Config.PageFactory.urlEditPage,
            params: {
                newStatus: 'delivered'
            },
            success: function (form, action) {
            	// TODO: move to a page.update() method
            	var values = form.getValues();            	
            	var page = Tg.PageFactory.findPage(values.id);            	
		        var node = Tg.PageFactory.pageTree.getNodeById(values.id);
		        node.setText(values.title);	
		        for (var property in values) {
		            Tg.PageFactory.pageTree.getNodeById(values.id).attributes[property] = values[property];	
		            page[property] = values[property];
		        }
            },
            failure: function (form, action) {
                switch (action.failureType) {
                    case Ext.form.Action.CLIENT_INVALID:
                        Ext.Msg.alert('Failure', 'Form fields may not be submitted with invalid values');
                        break;
                    case Ext.form.Action.CONNECT_FAILURE:
                        Ext.Msg.alert('Failure', 'Ajax communication failed');
                        break;
                    case Ext.form.Action.SERVER_INVALID:
                        Ext.Msg.alert('Failure', action.result.msg);
                }
            }
        });
    }

    , _sanitisePath: function (name) {
        name = name.Replace(":", "_");
        name = name.Replace("&", "_");
        name = name.Replace("/", "_");
        name = name.Replace("\\", "_");
        name = name.Replace(" ", "_");

        return name;
    }

	, deletePage: function (pageId) {
	    var page = this.findPage(pageId);
	    var _this = this;

	    Ext.Msg.confirm(
			'Delete?',
			'Are you sure you want to delete this page?',
			function (r) {
			    if (r == 'yes') {
			        var data = { "id": pageId };
			        $.ajax({ url: Tg.Config.PageFactory.urlDeletePage, data: data, type: 'POST' });
			        Tg.PageFactory.pageTree.getNodeById(pageId).parentNode.select();
			        Tg.PageFactory.pageTree.getNodeById(pageId).remove();
			    }
			});
	}
});

// overwrite
Tg.PageFactory = new Tg.PageFactory();

Ext.onReady(function() {
	//Tg.PageFactory.editPage (1);
});

