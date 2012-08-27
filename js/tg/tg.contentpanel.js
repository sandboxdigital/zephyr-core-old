
/**
* Tg.ContentPanel
* @extends Ext.Panel
* @author Thomas Garrood
*/

Tg.ContentPanelDefaults = {
	loadUrl : "/admin/content/load"
}

Tg.ContentPanel = Ext.extend(Ext.Panel, {
    url: ""
    ,hideVersions: false
    ,form: null
    ,currentVersion: 1
    ,versions: []
	,loadData : null

	, constructor: function (config) {
	    
	    this.tabPanel = new Ext.TabPanel ({
			region: 'center',
			xtype: 'tabpanel',
			stateful:true,
			items: [
                this.getTabContent()
				,this.getTabVersions()
                ,this.getTabPermissions()
			]
			});

	    config = config || {};
	    
	    config = Ext.apply(
            {
			cls:'CmsContentPanel',
			region: 'center',
			xtype: 'tabpanel',
			layout: 'fit',
			margins: '3 3 3 0',
			collapsed: false,
			items: this.tabPanel
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

    , getTabContent : function ()
    {
        //this.updateVersions (versions);

        this.versionsMenu = new Ext.menu.Menu({
            id: 'mainMenu',
            items: []
        });

        this.versionsButton = new Ext.Button({
            cls: 'x-btn-text', // text class
            text: 'Version: 1',
            menu: this.versionsMenu
        });

        var tbar = [];
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

        if (!this.hideVersions) {
            tbar.push("->");

//	        tbar.push({
//	            text: 'Publish',
//	            icon: '/core/images/icons/world.png',
//	            cls: 'x-btn-icon-text',
//	            scope: this,
//	            handler: this.onPublish
//	        });

            tbar.push(this.versionsButton);
        }

        return {
            title: 'Content',
            tbar: tbar,
            html: '<div id="contentPanelBody">Loading</div>',
            xtype: "panel",
            autoScroll: true
        }

    }

    , getTabVersions : function ()
    {
        this.grid = new Tg.VersionGrid({
            title: 'Versions',
            xtype: "versiongrid",
            autoScroll: true
        });
        this.grid.contentPanel = this;

        return this.grid;
    }

    , getTabPermissions : function ()
    {


//        var tbar = [];
//        tbar.push({
//            text: 'Save',
//            icon: '/core/images/icons/disk.png',
//            cls: 'x-btn-icon-text',
//            scope: this,
//            handler: this.onSave
//        });
//        tbar.push({
//            text: 'Preview',
//            icon: '/core/images/icons/page_white_magnify.png',
//            cls: 'x-btn-icon-text',
//            scope: this,
//            handler: this.onPreview
//        });
//
//        if (!this.hideVersions) {
//            tbar.push("->");
//
////	        tbar.push({
////	            text: 'Publish',
////	            icon: '/core/images/icons/world.png',
////	            cls: 'x-btn-icon-text',
////	            scope: this,
////	            handler: this.onPublish
////	        });
//
//            tbar.push(this.versionsButton);
//        }

//        {
//            title: 'Permissions',
//                tbar: tbar,
//            html: '<div id="contentPanelBody">Loading</div>',
//            xtype: "panel",
//            autoScroll: true
//        }
        return Tg.PermissionForm ();
    }

    , showSource: function () {
        this.form.isValid();
        var xml = this.form.toXml();

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
            , "onSave": jQuery.inScope(this.onSaveComplete, this)
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

    , loadVersion: function (version) 
    {
    	this.load(this.loadData, version); 
    }
    
    , load: function (data, version) 
    {
    	this.tabPanel.setActiveTab (0);
    	
        version = version || 0;

        this.createForm();
        this.mask();

        Ext.apply(data, { version: version });
        	
        this.loadData = data;
        $.ajax({
            url: Tg.ContentPanelDefaults.loadUrl,
            dataType: 'json',
            data: data,
            success: jQuery.inScope(this.onLoad, this)
        });
    }

    , onAfterRender: function () {
        //c("onAfterRender");
    }

	, onPreview: function () {
        this.mask();
        this.form.save()

        var path = Tg.PageFactory.findPath (pageTree.currentNode.attributes.id);
        window.open(path + "?version=-1", "tgPreview");
	}

    , onItemCheck: function (cb) {
    	if (this.currentVersion != cb.id) {
	        this.currentVersion = cb.id;
	        this.loadVersion(this.currentVersion);
    	} else
    		return false;
    }

    , onPublish: function () {
        this.mask();
        var template = Tg.PageFactory.findTemplate(pageTree.currentNode.attributes.templateId);

        var data = {
            "contentTemplateId": template.contentTemplateId,
            "contentId": "SitePage" + pageTree.currentNode.attributes.id,
            'showSubPages': false,
            "version": this.currentVersion,
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
    
    , onSaveComplete : function (response)
    {
    	this.currentVersion = response.data.currentVersion;
    	this.updateVersions (response.data.versions);
    	this.unmask ();
    }
    
    , onLoad : function (response)
    {
    	this.unmask ();
    	if (response.success) {
    		response.data.options['hideSave'] = "true";
	    	this.form.setOptions(response.data.options);
	    	this.form.loadXml (response.data.formXml);
	    	this.form.populateXml (response.data.dataXml);
	    	this.currentVersion = response.data.currentVersion;
	    	this.updateVersions (response.data.versions);
    	} else
    	{
    		this.form.clear ();
    		//alert (response.error);
    	}    	
    }

    , updateVersions : function (versions)
    {
        
    	this.versions = versions;
        if (this.hideVersions)
            return;
    
        this.versionsMenu.removeAll ();
    
        var foundPublished = false;
        var currentVersionName = "";

        for (var i=0;i<this.versions.length;i++)
        {
            var version = this.versions[i];
            var versionName = version.version;
            var versionStatus = '';
            var checked = false;
        
//            if (version.published)
//            {
//                versionName += " published";
//                foundPublished = true;
//            } else if (foundPublished)
//            {
//                versionName += " draft";
//            }

            if (i == 0)
            {
            	version.status = "Published";
            	versionStatus = " (published)";
            }
            
            if (this.currentVersion == version.version)
            {
                currentVersionName = versionName + versionStatus;
                checked = true;
            }

            this.versionsMenu.add(
             new Ext.menu.CheckItem({
                    id: version.version
                    ,text: versionName + versionStatus
                    ,checked:checked
                    ,checkHandler: this.onItemCheck
                    ,scope:this
                })
                );
        }

        this.versionsMenu.doLayout(); 

        this.versionsButton.setText ("Version: "+currentVersionName);
        
        // TODO - move to onTabClick or similar
        this.grid.getStore().loadData(versions);
    }
}); 


//example grid
Tg.VersionGrid = Ext.extend(Ext.grid.GridPanel, {
 contentPanel : null,
 
 initComponent:function() {
     var config = {
    		 tbar:[
	       /*{
	         text: 'Approve',
	         icon: '/core/images/icons/world.png',
	         cls: 'x-btn-icon-text'
	       },{
	        text: 'Preview',
	        icon: '/core/images/icons/page_white_magnify.png',
	        cls: 'x-btn-icon-text',
	        scope: this,
	        handler: this.onPreview
	    },*/
    	//'->'
	    /*,{
            text: 'Add',
            icon: '/core/images/icons/add.png',
            cls: 'x-btn-icon-text',
            scope: this,
            handler: this.handleToolbarAddPage
        }
        */
    	{
            text: 'Edit',
            icon: '/core/images/icons/edit.png',
            cls: 'x-btn-icon-text',
            scope: this,
            handler: this.onToolbarEditPage
        }
        /*, {
            text: 'Delete',
            icon: '/core/images/icons/cross.png',
            cls: 'x-btn-icon-text',
            scope: this,
            handler: this.handleToolbarDeletePage
        }*/
	    
	    ],
          store:new Ext.data.JsonStore({
        	  data:[
        	        {status:'Live',version:'1',date:'2/2/2011',author:'Superuser',approver:'Superuser'},
        	        {status:'',version:'1',date:'2/2/2011',author:'Superuser',approver:'Superuser'},
        	        {status:'',version:'1',date:'2/2/2011',author:'Superuser',approver:'Superuser'},
        	        {status:'',version:'1',date:'2/2/2011',author:'Superuser',approver:'Superuser'},
        	        {status:'',version:'1',date:'2/2/2011',author:'Superuser',approver:'Superuser'},
        	        {status:'',version:'1',date:'2/2/2011',author:'Superuser',approver:'Superuser'}
        	        ]
             ,id:'versions'
             ,fields:[
                  'status'
                 ,'version'
                 ,'approver'
                ,'author'
                ,{name:'created_at', type:'date'}
                 ,{name:'updated_at', type:'date'}
             ]
         })
         ,columns:[{
                 header:"Status"
                 ,width:20, 
                 sortable:true
                 ,dataIndex:'status'
             },{
              id:'version'
             ,header:"Version"
             ,width:20, 
             sortable:true
             ,dataIndex:'version'
         },{
             header:"Date"
            ,width:30, 
            sortable:true
            ,renderer:Ext.util.Format.dateRenderer('d/m/Y h:m:s')
            ,dataIndex:'updated_at'
         }/*,{
             header:"Author"
             ,width:30
             ,sortable:true
             ,dataIndex:'author'
         },{
             header:"Approver"
                 ,width:30
                 ,sortable:true
                 ,dataIndex:'approver'
             }*/]
         ,viewConfig:{forceFit:true}
         ,loadMask:true
         ,listeners : {
        	 rowclick:{fn:this.onRowClick,scope:this}
         }
                       
                       
     }; // eo config object

     // apply config
     Ext.apply(this, Ext.apply(this.initialConfig, config));

     // call parent
     Tg.VersionGrid.superclass.initComponent.apply(this, arguments);

     // load the store at the latest possible moment
//     this.on({
//         afterlayout:{scope:this, single:true, fn:function() {
//          //   this.store.load({params:{start:0, limit:10}});
//         }}
//     });

 } // eo function initComponent

,onRowClick : function (grid, rowIndex, e)
{
	this.rowIndex = rowIndex;
}

,onPublish : function ()
{
	
}

,onToolbarEditPage : function ()
{
	var row = this.getStore().getAt(this.rowIndex);
	this.contentPanel.loadVersion (row.data.version);
}
});

Ext.reg('versiongrid', Tg.VersionGrid);


Tg.PermissionForm = Ext.extend(Ext.form.FormPanel, {
    region: 'center',
    title: "Sample Form",
    layout: 'fit',
    doSomething: function(a) {
        this.getForm().findField('node').setValue(a);
    },
    doSomethingElse: function() {
        this.getForm().findField('node').setValue('');
    },
    initComponent: function() {
        Ext.apply(this, {
            items: {
                xtype: 'fieldset',
                labelPad: 10,
                defaultType: 'textfield',
                labelAlign: 'right',
                items: [
                    {
                        xtype: 'field',
                        id: 'node',
                        name: 'node',
                        fieldLabel: 'Selected Node',
                        labelSeparator: '',
                        width: 250
                    }
                ]
            }

        });
        Tg.PermissionForm.superclass.initComponent.call(this);

        //  Add a listener for nodeSelected event
        this.on('nodeSelected',
            function(a, b) {
                // call my method when nodeSelected event is fired
                this.doSomething(a)
            });

        //  Add a listener for rootSelected event
        this.on('rootSelected',
            function() {
                // call my method when rootSelected event is fired
                this.doSomethingElse()
            });
    }
});

Ext.reg('permissionForm', Tg.PermissionForm);
