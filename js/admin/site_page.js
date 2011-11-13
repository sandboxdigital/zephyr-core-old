
Ext.onReady(function () {
	pageTree = new Tg.PageTree ({});
	pageTree.on ('click', loadContent);

	var o = {
		leftCollapsed:false,
		heightOffset:145
	};

    // set up content panel
    form = createForm();

    var contentPanel = new Ext.Panel({
        region: 'center'
        , xtype: 'container'
  	    ,margins: '3 3 3 0'
        , cls: ''
        , items: [form]
    });
    
    doublePanel = new Tg.DoublePanel (pageTree, contentPanel, o);

    loadContent(pageTree.currentNode);

    Tg.PageFactory.pageTree = pageTree;
});

function loadContent(p) {
    var page = Tg.PageFactory.findPage(p.attributes.id);
    form.form.setValues(page);
}

function createForm() {
    var templates = new Array();
    Ext.each(Tg.PageFactory.templates, function (el) {
        templates.push(new Array(el.id, el.name));
    });

    var themes = new Array();
    Ext.each(Tg.PageFactory.themes, function (el) {
        themes.push(new Array(el.id, el.name));
    });

    return new Ext.FormPanel({
        title: 'Page details',
        frame: true,
        labelWidth: 110,
        bodyStyle: 'padding:0 10px 0;',
        labelAlign: 'right',
        waitMsgTarget: true,
        defaultType: 'textfield'
        , buttons: [{
            text: 'Save',
            handler: function () {
                Tg.PageFactory.editPageSave(form.getForm());
            }
        }]
        , items: [
            {
                xtype: 'fieldset',
                title: 'General',
                autoHeight: true,
                layout: 'form',
                items: [
				    new Ext.form.Hidden({
				        name: 'id'
				    }),
				    new Ext.form.Hidden({
				        name: 'parentId'
				    }),
		            {
		                xtype: 'textfield',
		                fieldLabel: 'Title',
		                name: 'title',
		                anchor: '95%'
		            },
				    {
				        xtype: 'textfield',
				        fieldLabel: 'Path',
				        name: 'name',
				        anchor: '95%'
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
				        anchor: '95%'
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
				        anchor: '95%'
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
				        anchor: '95%'
				    })]
            },
            {
                xtype: 'fieldset',
                title: 'Meta data',
                autoHeight: true,
                layout: 'form',
                collapsed: true,   // initially collapse the group
                collapsible: true,
                items: [{
                    xtype: 'textfield',
                    name: 'metaTitle',
                    fieldLabel: 'Title',
                    anchor: '95%'
                }
                , {
                    xtype: 'textfield',
                    name: 'metaDescription',
                    fieldLabel: 'Description',
                    anchor: '95%'
                }, {
                    xtype: 'textfield',
                    name: 'metaKeywords',
                    fieldLabel: 'Keywords',
                    anchor: '95%'
                }]
            },
            {
                xtype: 'fieldset',
                title: 'Advanced',
                autoHeight: true,
                layout: 'form',
                collapsed: true,   // initially collapse the group
                collapsible: true,
                items: [{
                    xtype: 'textfield',
                    name: 'action',
                    fieldLabel: 'Override action',
                    anchor: '95%'
                }
                /*, {
                    xtype: 'textfield',
                    name: 'metaDescription',
                    fieldLabel: 'Description',
                    anchor: '95%'
                }, {
                    xtype: 'textfield',
                    name: 'metaKeywords',
                    fieldLabel: 'Keywords',
                    anchor: '95%'
                }*/]
            }]
    });
}