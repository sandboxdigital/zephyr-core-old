// singleton class for managing folders
// extends Ext.util.Observable

Tg.TreePanel = Ext.extend (Ext.tree.TreePanel, {
	currentNode:null
		
	,constructor: function(config) {
		Tg.TreePanel.superclass.constructor.call(this, config);
		
		this.on('click', this.onClick, this);
        this.on('afterrender', this.handleAfterRender, this);
    }

	,handleAfterRender : function ()
	{        
	   this.restoreState();
	}
	
	,restoreState : function(defaultPath) 
	{	
		var currentNodeId =  Ext.state.Manager.get("tgTreePanel_"+this.id+"_node", '0');
		
		// note nodes have to be expanded so the populate this.nodeHase
	     this.currentNode = this.getNodeById(currentNodeId+"");
        
        if (!this.currentNode)
            this.currentNode = this.getRootNode();

        this.currentNode.select();
	}

	,onClick : function(node) {
		this.currentNode = node
		Ext.state.Manager.set("tgTreePanel_"+this.id+"_node", this.currentNode.id);;
	}
});



// Singleton Observable!


Tg.FileFactory = function () {
    var defaultConfig = {
        urlAddFolder: "/admin/file-folder-add"
        , urlEditFolder: "/admin/file-folder-edit"
        , urlDeleteFolder: "/admin/file-folder-delete"
        , urlMoveFolder: "/admin/file-folder-move"
        , urlFolderList: '/admin/file-folder-list'
        , urlFolderAddFile: '/admin/file-folder-add-file'
        , urlFolderRemoveFile: '/admin/file-folder-remove-file'
        , urlFileImport: '/admin/files/import'
        , urlFileUploadValums: '/admin/file-upload-valums'
        , urlFolderFileList: '/admin/file-list'
    };

    var config = Tg.Config.FileFactory || {};
    var config = Ext.apply(defaultConfig, config);

    return Ext.apply(new Ext.util.Observable(), {
        unCatname: 'uncategorised'
	    , rootFolder: null
	    , files: null
	    , fileTree: null
        , config: config

        //    , constructor: function (config) {
        //        alert("!")
        //        c(config);
        //        // call parent init component
        //        Tg.FileFactory.superclass.initComponent.apply(this, arguments);

        //        // add custom events
        //        this.addEvents('foldersLoaded');
        //    }

	, loadFolders: function () {
	    c(this);

	    var self = this;
	    Ext.Ajax.request({
	        url: this.config.urlFolderList + '?v=' + Math.random(),
	        success: this.foldersLoaded.createDelegate(this),
	        failure: function (obj, data) {
	            c(data);
	        },
	        headers: {
	            'my-header': 'foo'
	        },
	        params: { foo: 'bar' }
	    });
	}

	, foldersLoaded: function (resp) {
	    this.setFolders(Ext.decode(resp.responseText));

	    this.fireEvent('foldersLoaded', this);
	}

	, setFolders: function (rootFolder) {
	    this.rootFolder = this.createTreeNode(rootFolder);
	    this.rootFolder.appendChild(new Tg.Folder({ id: -1, title: 'Uncategorised' }))
	}

	, createTreeNode: function (json) {
	    var root = new Tg.Folder(json);

	    for (var i = 0; i < json.children.length; i++) {
	        var sub = this.createTreeNode(json.children[i]);
	        root.appendChild(sub);
	    }

	    return root;
	}

	, setFiles: function (files) {
	    this.files = files;
	}

	, findFolder: function (folderId) {
	    if (folderId == this.rootFolder.id)
	        return this.rootFolder;
	    return this.rootFolder.findChild("id", folderId, true);
	}

	, _getForm: function () {

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
				}
			]
	    });
	}

	, addFolder: function (parentId) {
	    var folder = this.findFolder(parentId);

	    var form = this._getForm();

	    var folderClone = {
	        'id': 0
			, 'parentId': parentId
	    };

	    form.form.setValues(folderClone);

	    var win = new Tg.FormWindow(form);
	    win.url = this.config.urlAddFolder;
	    win.show();
	    win.focus();
	    win.on('save', function (formPanel, values, results) {
	        folder.addChild(results.folder);
	    });
	}

	, editFolder: function (folderId) {
	    var folder = this.findFolder(folderId);
	    var form = this._getForm();
	    form.form.setValues(folder.attributes);

	    var win = new Tg.FormWindow(form);
	    win.url = this.config.urlEditFolder;
	    win.show();
	    win.focus();
	    win.on('save', function (formPanel, values, results) {
	        folder.update(results.folder);
	    });
	}

	, deleteFolder: function (folderId) {
	    var folder = this.findFolder(folderId);
	    var _this = this;

	    Ext.Msg.confirm(
			'Delete?',
			'Are you sure you want to delete this folder?\n\nFiles will be moved to \'' + this.uncatName + '\'',
			function (r) {
			    if (r == 'yes') {
			        var data = { "id": folderId };
			        $.ajax({ url: _this.config.urlDeleteFolder, data: data, type: 'POST' });
			        Tg.FileFactory.folderTree.getNodeById(folderId).parentNode.select();
			        Tg.FileFactory.folderTree.getNodeById(folderId).remove();
			    }
			});
	}

	, addFileToFolder: function (fileId, folderId) {
	    Ext.Ajax.request({
	        url: this.config.urlFolderAddFile
			   , params: { folderId: folderId, fileId: fileId }
	    });
	}

	, removeFileFromFolder: function (fileId, folderId) {
	    Ext.Ajax.request({
	        url: this.config.urlFolderRemoveFile
		, params: { folderId: folderId, fileId: fileId }
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
    });
} ();

Tg.Folder = Ext.extend(Ext.tree.TreeNode, {
	children : []
	
	,constructor: function (data) {
        data['text'] = data['title'];
        data['allowChildren'] = true;
        data['expanded'] = Ext.state.Manager.get('tgFolderTree_node_'+data['id'],'false')=='true';
        
		Tg.Folder.superclass.constructor.call(this, data);

	    this.on('expand', this.onExpand, this);
	    this.on('collapse', this.onCollapse, this);
	}

	,addChild : function (json)
	{
		var folder = new Tg.Folder(json);
        this.appendChild(folder);
        this.children.push(folder);
	}
		
	,update : function (json)
	{
//	    this.setId(json.title);
	    this.setText(json.title);
	    
	    this.attributes = json;
	}
	
	,onExpand : function ()
	{
        Ext.state.Manager.set('tgFolderTree_node_'+this.id,'true');
	}
	
	,onCollapse : function ()
	{
        Ext.state.Manager.set('tgFolderTree_node_'+this.id,'false');
	}
});

Tg.FolderTree = Ext.extend(Tg.TreePanel, {
    border: false
    ,rootNode: null

    , constructor: function (config) {
        this.rootNode = Tg.FileFactory.rootFolder;
        this.rootNode.draggable = false;

        // constructor pre-processing
        config = config || {};
        config.listeners = config.listeners || {};
        config.tbar = config.tbar || {};
//        Ext.apply(config.listeners, {
//            click: { fn: this.handleNodeClick, scope: this },
//            dblclick: { fn: this.handleToolbarEditFolder, scope: this },
//            expandnode: { fn: this.handleNodeExpand, scope: this },
//            collapsenode: { fn: this.handleNodeCollapse, scope: this }
//        });

        Ext.apply(config, {
            autoScroll: true,
            animate: true,
            containerScroll: true,
            border: false,
            root: this.rootNode,
            
            listeners:{
        		afterrender:this.onAfterRender
        	},
           
            tbar: [{
                text: 'Add',
                icon: '/core/images/icons/add.png',
                cls: 'x-btn-icon-text',
                scope: this,
                handler: this.handleToolbarAddFolder
            }
            , {
                text: 'Edit',
                icon: '/core/images/icons/edit.png',
                cls: 'x-btn-icon-text',
                scope: this,
                handler: this.handleToolbarEditFolder
            }
            , {
                text: 'Delete',
                icon: '/core/images/icons/cross.png',
                cls: 'x-btn-icon-text',
                scope: this,
                handler: this.handleToolbarDeleteFolder
            }
            ]
        });

        // call parent contructor
        Tg.FolderTree.superclass.constructor.apply(this, arguments);

//        this.on('startdrag', this.handleNodeStartDrag, this);
//        this.on('movenode', this.handleNodeMove, this);
//        this.on('afterrender', this.handleAfterRender, this);
//
//        var url = document.location.href;
//        if (url.indexOf('#') > 0) {
//            this.currentNodeId = url.substring(url.indexOf('#') + 1);
//        }
//
//        if (!this.currentNodeId)
//            this.currentNodeId = Ext.state.Manager.get('tgFolderTree_current', Tg.FileFactory.rootFolder.id);

        Tg.FileFactory.folderTree = this;
    }

//    , initComponent: function () {
//        var config = {};
//
//        Ext.apply(this, config);
//        Ext.apply(this.initialConfig, config);
//
//        Tg.FolderTree.superclass.initComponent.apply(this, arguments);
//    }

//    , handleNodeStartDrag: function (tree, node, event) {
//        oldPosition = node.parentNode.indexOf(node);
//        oldNextSibling = node.nextSibling;
//    }

//    , handleNodeMove: function (tree, node, oldParent, newParent, position) {
//        var previousSiblingId = node.previousSibling != null ? node.previousSibling.id : ''; // if no previous sibling move to the first child
//
//        var url = Tg.FileFactory.urlMoveFolder;
//
//        var params = { 'folderId': node.id, 'parentId': newParent.id, 'previousSiblingId': previousSiblingId };
//
//        //         we disable tree interaction until we've heard a response from the server
//        //         this prevents concurrent requests which could yield unusual results
//
//        //        tree.disable();
//
//        Ext.Ajax.request({
//            url: url,
//            params: params,
//            success: function (response, request) {
//
//                // if the first char of our response is not 1, then we fail the operation,
//                // otherwise we re-enable the tree
//
//                //                if (response.responseText.charAt(0) != 1) {
//                //                    request.failure();
//                //                } else {
//                tree.enable();
//                //                }
//            },
//            failure: function () {
//
//                // we move the node back to where it was beforehand and
//                // we suspendEvents() so that we don't get stuck in a possible infinite loop
//
//                tree.suspendEvents();
//                oldParent.appendChild(node);
//                if (oldNextSibling) {
//                    oldParent.insertBefore(node, oldNextSibling);
//                }
//
//                tree.resumeEvents();
//                tree.enable();
//
//                alert("Oh no! Your changes could not be saved!");
//            }
//        });
//    }

//    , handleNodeClick: function (n) 
//    {
//        this.currentNode = n;
//        this.currentNodeId = n.attributes.id;
//        Ext.state.Manager.get('tgFolderTree_current', this.currentNodeId);
//        
//        c(Ext.state.Manager);
//
//        document.location.hash = "#" + this.currentNodeId;
//    }
//
//    , handleNodeExpand: function (n) {
//        var folderId = n.attributes.id + "";
//        Ext.state.Manager.set('tgFolderTree_node_'+folderId, "true");
//    }
//
//    , handleNodeCollapse: function (n) {
//        var folderId = n.attributes.id + "";
//        Ext.state.Manager.set('tgFolderTree_node_'+folderId, "true");
//    }

	, onAfterRender : function () {
		var self = this;
		
		this.dropZone = new Ext.dd.DropZone(this.getEl(), {
			ddGroup:'files',

	        getTargetFromEvent: function(e) {
	            return e.getTarget('.x-tree-node-el');
	        },

	        onNodeEnter : function(target, dd, e, data){
	            Ext.fly(target).addClass('hospital-target-hover');
	        },

	        onNodeOut : function(target, dd, e, data){
	            Ext.fly(target).removeClass('hospital-target-hover');
	        },

	        onNodeOver : function(target, dd, e, data){
	            return Ext.dd.DropZone.prototype.dropAllowed;
	        },

	        onNodeDrop : function(target, ddSource, e, data){
                var records =  ddSource.dragData.selections;
                
                c(records);
                
                // remove from grid

				var folderId = Ext.Element.get(target).getAttribute("tree-node-id","ext");

				if (folderId != self.currentNode.id)
				{
					Ext.each(records, function (record){
						c(record)
	    	        	var fileId = record.id;
						Tg.FileFactory.addFileToFolder (fileId, folderId);
						Tg.FileFactory.removeFileFromFolder (fileId, self.currentNode.id);
					});
    	        }
				
                Ext.each(records, ddSource.grid.store.remove, ddSource.grid.store);
                
	            return true;
	        }
	    });
	}

    , handleToolbarAddFolder: function () {
        Tg.FileFactory.addFolder(this.currentNode.attributes.id);
    }

    , handleToolbarEditFolder: function (n) {
        if (n instanceof Ext.tree.TreePanel)
            this.currentNode = n;
        Tg.FileFactory.editFolder(this.currentNode.attributes.id);
    }

    , handleToolbarDeleteFolder: function () {
        Tg.FileFactory.deleteFolder(this.currentNode.attributes.id);
    }
}); 
 
// register xtype
Ext.reg('Tg.FolderTree', Tg.FolderTree); 

Ext.onReady(function() {
	//Tg.FileFactory.editfolder (1);
});


