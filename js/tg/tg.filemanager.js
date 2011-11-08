// singleton class for managing folders
// extends Ext.util.Observable

// singleton class for managing folders
// extends Ext.util.Observable


Tg.File = {}
Tg.File.Windows = {}

Tg.FileManager = Ext.extend(Ext.util.Observable, {
    fileBrowserWindow: null,
    defaults: {
        container: 'content-div'
		, leftTitle: 'Folders'
    }

	, constructor: function (options) {

	}

	, showBrowser: function (config) {
	    config = config || {};
	    if (Tg.Config.FileBrowser)
	        config = Ext.apply(config, Tg.Config.FileBrowser);

	    fileBrowser = new Tg.FileBrowser(config);
	}

	, showBrowserInWindow: function (config) {
	    if (Tg.Config.FileBrowser)
	        config = Ext.apply(config, Tg.Config.FileBrowser);

	    if (this.fileBrowserWindow == null)
	        this.fileBrowserWindow = new Tg.WindowedFileManager(config);
	    else
	        this.fileBrowserWindow.setCallback(config['onSelect']);

	    this.fileBrowserWindow.show();
	}

	, filePreviewRow: function (file) {
	    var url = file.url;
	    if (file.thumbnailUrl != undefined) {
	        url = file.thumbnailUrl;
	    }
	    var ext = Tg.FileUtils.getExtension(url);

	    if (ext != "jpg" && ext != "jpeg" && ext != "png" && ext != "gif")
	        url = "/core/images/fileicons/" + ext + ".png";

	    var xhtml = '<tr>';
	    xhtml += '<td class="fileUploadThumbnail"><img id="' + this._id + '_image" src="' + url + '" width="32" height="32"  /></td>';
	    xhtml += '<td id="' + this._id + '_name" class="fileUploadName">' + file.name + '</td>';
	    xhtml += '<td class="fileUploadDelete">';
	    xhtml += '<a href="#" class="formFileUploadDelete">Remove this file</a>';
	    xhtml += '</td>';
	    xhtml += '</tr>';

	    return xhtml;
	}
});

// overwrite
Tg.FileManager = new Tg.FileManager();

Tg.FileUploadField =  Ext.extend(Ext.util.Observable, {
	
	constructor: function (config) 
	{
		this.config = config;
		this._preview = $('#'+this.config['preview']);
		this._hidden = $('#'+this.config['hidden']);
		Ext.Element.get(this.config['upload']).on("click", this.onClick, this);
	}

	, onClick : function ()
	{
    	var options = {
    		onSelect: this.onSelect.createDelegate (this)
    		,listConfig: {
    			showSelect:true
    		}
    	}
    	Tg.FileManager.showBrowserInWindow (options);
	}
	
	, onSelect : function (file)
	{	
    	if (Tg.FileManager.fileBrowserWindow)
    		Tg.FileManager.fileBrowserWindow.hide ();
    	
		this.addFile (file);
	}
	
	, addFile : function (file)
	{    	
		this._preview.empty();
		this._hidden.val (file.id);
    	
		var xhtml = Tg.FileManager.filePreviewRow(file);
		
		this._preview.append(xhtml);
		
		var self = this;
	    this._preview.find('.formFileUploadDelete').bind('click', function () {
	        if (confirm ('Are you sure you want to remove this file?'))
	    	{
	        	self.removeFile ();
	    	}
	        return false;
	    });
		$('#' + this.elPath + '_FormFileButtons').css({float:'right'});
	}
	
	, removeFile : function () {
		this._hidden.val('0');
		this._preview.find('tr').fadeOut ();
	}
});

Tg.FileBrowser = Ext.extend(Ext.util.Observable, {
	fileUploadWindow:null
	,uploader:null
	,options:null
	,defaults : {
		container:'content-div'
		,leftTitle:'Folders'
		,uploadButton:true
		,importButton:false
		,list:{}
	}
	
	, constructor: function (options) 
	{
		this.options = $.extend ({}, this.defaults, options || {})
	
		if (Tg.FileFactory.rootFolder != null)
		{
			this.create ();
		} else
		{
			Tg.FileFactory.on ('foldersLoaded', this.create, this)
			Tg.FileFactory.loadFolders ();
		}
	}

	,create : function ()
	{
		this.folderTree = new Tg.FolderTree ({});
		this.folderTree.on ('click', this.loadContent, this);
	    
		this.fileList = new Tg.File.FileList (this.options.list);
		this.fileList.on ('fileSelected', this.onFileSelected, this);
		
	    var config = 
	        {
				cls:'CmsContentPanel',
				region: 'center',
				xtype: 'container',
				layout: 'fit',
		        title: 'Files',
				margins: '3 3 3 0',
				collapsed: false,
				items: [
				        this.fileList
				        ]
	        };
	    
	    config['tbar'] = [];
	    
	    config['tbar'].push({
            text: 'Delete',
            icon: '/core/images/icons/cross.png',
            cls: 'x-btn-icon-text',
            scope: this,
            handler: this.deleteFiles
        });
	    

    	config['tbar'].push('->');
	    
	    if (this.options['uploadButton'])
    	{	
            config['tbar'].push({
                text: 'Upload file(s)',
                icon: '/core/images/icons/disk.png',
                cls: 'x-btn-icon-text',
                scope: this,
                handler: this.btnUploadFiles
            });
    	}
	    
	    if (this.options['importButton'])
    	{	
	    	config['tbar'].push('|');
	    	config['tbar'].push({
                text: 'Import file(s)',
                icon: '/core/images/icons/world.png',
                cls: 'x-btn-icon-text',
                scope: this,
                handler: this.importFiles
            });
    	}
	    
	    this.contentPanel = new Ext.Panel (config);
	    doublePanel = new Tg.DoublePanel (this.folderTree, this.contentPanel, this.options);
	    
		// load files
		this.loadContent (this.folderTree.currentNode);
		if (Ext.Element.get('btnUploadFiles'))
			Ext.Element.get('btnUploadFiles').on ('click',this.btnUploadFiles, this);
		if (Ext.Element.get('btnImportFiles'))
			Ext.Element.get('btnImportFiles').on ('click',this.importFiles, this);
	}
	
    , unmask: function () {
    	this.contentPanel.el.unmask();
    }

    , mask: function () {
    	this.contentPanel.el.mask('Working...', 'loadingMask');
    }
	
	, deleteFiles : function ()
	{
		this.fileList.deleteSelected();
	}
	
	, importFiles : function ()
	{
		var self = this;
		var win = new Tg.File.Windows.Import ();
		win.show ();
		win.loadUrl (Tg.FileFactory.config.urlFileImport,{folderId:this.folderTree.currentNode.id});
		win.on ("submitted", this.refresh, this);
	}
	
	, onFileSelected : function (list, file)
	{
		if (this.options['onSelect'])
			this.options['onSelect'].call(this, file);
		
		return false;
	}
	
	, btnUploadFiles : function () {
		var self = this;
		if (this.fileUploadWindow == null) {		
			this.fileUploadWindow = new Ext.Window({
				title:'Upload files',
				width:500,
				height:380,
				stateful:false,
				constrainHeader:true,
				modal:true,
				closeAction: 'hide',
				listeners: {
					afterrender:{ fn:this.createUploader, scope:this}
					,beforeclose:{ fn:this.beforeClose, scope:this}
				},
				buttons: [
					{
						text: 'Close',
						handler:function() {self.fileUploadWindow.hide ();}
					}
				],
				html: '<div id="fileUploadWindowContent" style="padding:20px;"><div id="file-uploader"></div></div>'					
				});
		} else
			this.uploader.setParams({folderId:this.folderTree.currentNode.id});
			
		this.fileUploadWindow.show();
	}

	,beforeClose : function ()
	{
		if (this.uploader.getInProgress()>0)
		{
			if (confirm("The files are being uploaded, if you close now the upload will be cancelled.\n\nDo you want to close?"))
				return false;
		}
		this.uploader.getHandler().cancelAll();
			
		return true;
	}
	
	,createUploader : function ()
	{
		var self = this;
		this.uploader = new qq.FileUploader({
			element: document.getElementById('file-uploader'),
			action: Tg.FileFactory.config.urlFileUploadValums,
//			action: '/core/js/valums/test.php',
//			action: '/upload.php',
			maxConnections : 1,
			onComplete: this.uploadFile.createDelegate(this),
			debug: true
		});
		this.uploader.setParams({folderId:this.folderTree.currentNode.id});
	}
	
	, uploadFile : function (var1, var2, result) 
	{
		if (result.success)
		{
			this.fileList.add (result.file);
		} 
		else 
		{
			
		}
		return true;
	}

	, loadContent : function (folderNode)
	{
		//this.mask();
		var folderId = folderNode.attributes.id;
		this.fileList.load(folderId);
	}
});

Tg.File.Windows.Import = Ext.extend(Tg.Window, {
	constructor: function(config) 
	{
		var config = {
			submitLabel:'Import'
		}
    	Tg.File.Windows.Import.superclass.constructor.call(this, config);
	}
});


Tg.WindowedFileManager = Ext.extend(Ext.Window, {
	fileBrowser:null,
	config:null,
	defaults : {
		container:'fileBrowserWindowContent'
		,list:{
			showSelect:true
		}
		,leftTitle:'Folders'
	}
	,browserConfig : {}

	,constructor: function(browserConfig, windowConfig) 
	{
		browserConfig = browserConfig || {};
		windowConfig = windowConfig || {};
		this.browserConfig = Ext.apply (browserConfig, this.defaults);
		
	    var windowConfig = {
			id:'FileBrowserWindow',
			title:'Files',
			width:750,
			height:650,
			constrainHeader:true,
			modal:true,
			closeAction: 'hide',
			listeners: {
				afterrender:{ fn: this.handleAfterRender, scope: this }
				,beforeclose:{ fn: this.handleBeforeClose, scope: this }
			},
			buttons: [
				{
					text: 'Close',
					scope:this,
					handler:this.handleClose
				}
			],
			html: '<div id="fileBrowserWindowContent"></div>'					
			};
	
	    Tg.WindowedFileManager.superclass.constructor.call(this, windowConfig);
	}
	
	,handleClose : function ()
	{
		this.hide ();
	}	
	
	,handleAfterRender : function ()
	{
		this.browserConfig.height = this.getInnerHeight ();
		this.fileBrowser = new Tg.FileBrowser (this.browserConfig);
	}
	
	,handleBeforeClose : function ()
	{
		//this.hide ();
	}
	
	,setCallback : function (fn)
	{
		this.fileBrowser.options['onSelect'] = fn;
	}
});


Tg.File.FileList = Ext.extend(Ext.grid.GridPanel, {
	folderId:null
	,defaults : {
	    showSelect:false
	}
	
	,constructor: function(initialConfig) 
	{
		this.initialConfig = initialConfig;
		var self = this;
	 	function buttonRender (value, id, r)
	 	{
            var id = Ext.id();
            createGridButton.defer(1, this, ['Choose', id, r]);
            return('<div id="' + id + '"></div>');
	 	}
	 	function createGridButton(value, id, record) {
	        new Ext.Button({
	            text: value
	            ,handler : function(btn, e) {
	                self._select(record);
	            }
	        }).render(document.body, id);
	    }
	 	function renderThumb (thumbnailUrl)
	 	{
	 		return '<img src="'+thumbnailUrl+'" width="48" height="48" />';
	 	}
	 	
	    var strData = new Ext.ux.data.PagingJsonStore({
	        url: Tg.FileFactory.config.urlFolderFileList
	        ,fields: [
	            {name:'id',type:'integer'}, 
	            'name', 
	            'fullname', 
	            'type', 
	            'url', 
	            'thumbnailUrl'
	        ]
	    });
	 	
	    var columns = [
	       	  	    {header: 'Id', width:50, sortable: true, dataIndex: 'id'},
	    	        {header: 'Preview', width:70, sortable: false, renderer:renderThumb, dataIndex: 'thumbnailUrl'},
	    	        {header: 'Name', width:250, sortable: true, dataIndex: 'name', id:'name'}
	    	    ];
	    
	    if(this.initialConfig["showSelect"]==true)
	    	columns.push({header: '', width:70, sortable: false, dataIndex: 'id', renderer:buttonRender});
		
	    var config = {
		    columns: columns
    	    ,autoExpandColumn:'name'
            ,store:strData
            ,enableDragDrop:true
            ,ddGroup : 'files'
            ,bbar: [
              new Ext.ux.PagingToolbar({
                  store: strData
              })
            ]
        };
		
		
		Tg.File.FileList.superclass.constructor.call(this, config);
		this.on("afterrender", this.onAfterRender, this);		
	}

	, onAfterRender : function ()
	{
//		c (this.getView().dragZone.setGroup(''));
//		Tg.File.FileList.superclass.onAfterRender.call(this, config);
	}

	, load : function (folderId)
	{
		this.folderId = folderId;
		this.store.baseParams = {folderId:folderId};
		this.store.load({
		    params: {start: 0, limit: 25}
		});
	}
	
	, getSelected : function ()
	{
        var index = this.getSelectionModel().getSelections();
        return index;
	}
	
	, deleteSelected : function ()
	{
		if (confirm("Are you sure you want to remove these files?\n\nThis will only remove the file(s) from this folder")) {
	        var records = this.getSelectionModel().getSelections();
	        
	        for (var i in records)
	        {
	    		Tg.FileFactory.removeFileFromFolder (records[i].id, this.folderId);
	        }
	        
	        this.store.remove(records);
		}
	}
	
	, add : function (file) 
	{
        var u = new this.store.recordType(file);
        this.store.insert(0, u);
    }
	
	,_select : function (record)
	{
		this.fireEvent('fileSelected',this, record.data)
	}
});

Ext.reg('tg.file.filelist', Tg.File.FileList); 











