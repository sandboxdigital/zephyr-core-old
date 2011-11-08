/**
* Tg.ContentPanel
* @extends Ext.Panel
* @author Thomas Garrood
*/

function updateVersions() {

}

Tg.ContentPanelDefaults = {
	loadUrl : "/Admin/Content/Capture"
}

Tg.ContentPanel = Ext.extend(Ext.Panel, {
    url: ""
    , hideVersions: true
    , form: null
    , currentVersion: 1
    , versions: []

	, constructor: function (config) {
	    //this.updateVersions (versions);

	    var versionsMenu = new Ext.menu.Menu({
	        id: 'mainMenu',
	        items: []
	    });

	    var versionsButton = new Ext.Button({
	        cls: 'x-btn-text', // text class
	        text: 'Version: 1',
	        menu: versionsMenu
	    });

	    var tbar = [];

	    if (!this.hideVersions) {
	        tbar.push("|");

	        tbar.push({
	            text: 'Publish',
	            icon: '/core/images/icons/world.png',
	            cls: 'x-btn-icon-text',
	            scope: this,
	            handler: this.onPublish
	        });

	        tbar.push(versionsButton);
	    }

	    tbar.push("->");
	    tbar.push({
	        text: 'Save',
	        icon: '/core/images/icons/disk.png',
	        cls: 'x-btn-icon-text',
	        scope: this,
	        handler: this.onSave
	    });
	    tbar.push({
	        text: 'Preview',
	        icon: '/core/images/icons/page_white_magnify.png',
	        cls: 'x-btn-icon-text',
	        scope: this,
	        handler: this.onPreview
	    });

	    config = config || {};
	    
	    config = Ext.apply(
            {
            	cls:'CmsContentPanel',
                region: 'center',
                xtype: 'container',
                layout: 'fit',
     	       margins: '3 3 3 0',
                collapsed: false,
		        tbar: tbar,
                items: [{
                    html: '<div id="contentPanelBody">Loading</div>',
	                xtype: "panel",
                    autoScroll: true
                }]
            }, config);

	    Tg.ContentPanel.superclass.constructor.call(this, config);

	    this.on('afterrender', this.onAfterRender, this);

	    var map = new Ext.KeyMap(Ext.get(document), [{
	        key: "u",
	        alt: true,
	        scope: this,
	        fn: this.showSource
	    }
        ]);
	}

    , showSource: function () {
        this.form.isValid();
        var xml = this.form.toXml();

        c(xml);

        var win = new Ext.Window({
            autoScroll: false,
            width: 500,
            height: 300,
            buttons: [
				{
				    text: 'Save',
				    scope: this,
				    handler: this.sourceSave
				}
			]
            , html: '<textarea class="" style="width:100%;height:100%;overflow:scroll;" id="cmsFormSource">' + xml + '</textarea>'
        });
        win.show();
    }

    , sourceSave: function () {
        var xml = $('#cmsFormSource').val();
        this.form.saveAjax(xml, this.form.onSave);
    }

    , createForm: function () {
        if (this.form != null)
            return;

        var formOptions = {
            'url': '/Admin/Content/Save'
            , 'subForm': false
            , 'hideSave': 'true'
            , 'container': '#contentPanelBody'
            , "onSave": jQuery.inScope(this.unmask, this)
        };
        form = new CMS.Form(formOptions);

        this.form = form;
    }

    , unmask: function () {
        this.el.unmask();
    }

    , mask: function () {
        this.el.mask('Working...', 'loadingMask');
    }

    , onAfterRender: function () {
        //c("onAfterRender");
    }

	, onPreview: function (cb) {
	    window.open(pageTree.currentNode.attributes.url + "?version=-1", "tgPreview");
	}

    , onItemCheck: function (cb) {
        this.currentVersion = cb.id;
        this.updateVersions(this.versions);
        this.load(pageTree.currentNode, currentVersion);
    }

    , onPublish: function () {
        this.mask();
        var template = Tg.PageFactory.findTemplate(pageTree.currentNode.attributes.templateId);

        var data = {
            "contentTemplateId": template.contentTemplateId,
            "contentId": "SitePage" + pageTree.currentNode.attributes.id,
            'showSubPages': false,
            "version": currentVersion,
            'hideSave': 'true'
        };

        $.ajax({
            url: "/Admin/Content/Publish",
            dataType: 'script',
            data: data,
            success: jQuery.inScope(this.unmask, this)
        });
    }

    , onSave: function () {
        this.mask();
        this.form.save()
    }

    , load: function (data, version) {
        version = version || 0;

        this.createForm();
        this.mask();

        Ext.apply(data, { version: version });

        $.ajax({
            url: Tg.ContentPanelDefaults.loadUrl,
            dataType: 'json',
            data: data,
            success: jQuery.inScope(this.onLoad, this)
        });
    }
    
    , onLoad : function (reponse)
    {
    	this.unmask ();
    	if (reponse.success) {
    		reponse.data.options['hideSave'] = "true";
	    	this.form.setOptions(reponse.data.options);
	    	this.form.loadXml (reponse.data.formXml);
	    	this.form.populateXml (reponse.data.dataXml);
    	} else
    	{
    		this.form.clear ();
    	}    	
    }

    //    , updateVersions : function (versions)
    //    {
    //        if (hideVersions)
    //            return;
    //    
    //        versionsMenu.removeAll ();
    //    
    //        var foundPublished = false;
    //        var currentVersionName = "";

    //        for (var i=0;i<versions.length;i++)
    //        {
    //            var version = versions[i];
    //            var versionName = version.version;
    //            var checked = false;
    //        
    //            if (version.published)
    //            {
    //                versionName += " published";
    //                foundPublished = true;
    //            } else if (foundPublished)
    //            {
    //                versionName += " draft";
    //            }

    //            if (currentVersion == version.version)
    //            {
    //                currentVersionName = versionName;
    //                checked = true;
    //            }

    //            versionsMenu.add(
    //             new Ext.menu.CheckItem({
    //                    id: version.version
    //                    ,text: versionName
    //                    ,checked:checked
    //                    ,checkHandler: onItemCheck
    //                })
    //                );
    //        }

    //        versionsMenu.doLayout(); 

    //        versionsButton.setText ("Version: "+currentVersionName);
    //    }
}); 
