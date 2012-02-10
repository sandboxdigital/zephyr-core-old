
Tg.Page = Ext.extend(Ext.tree.TreeNode, {
	constructor: function (data) {
		data.text = data.title;
		Tg.Page.superclass.constructor.call(this, data);
	}
});

Tg.PageTree = Ext.extend(Ext.tree.TreePanel, {
    border: false,
    expandedNodes: null,
    currentNode: null,
    currentNodeId: null,
    rootNode: null

    , constructor: function (config) {
        if ($.cookie('tgPageTree_expanded'))
            this.expandedNodes = $.cookie('tgPageTree_expanded').split(",");
        else
            this.expandedNodes = new Array();

        this.rootNode = this.createTreeNode(Tg.PageFactory.pages);
        this.rootNode.draggable = false;

        // constructor pre-processing
        config = config || {};
        config.listeners = config.listeners || {};
        config.tbar = config.tbar || {};
        Ext.apply(config.listeners, {
            click: { fn: this.handleNodeClick, scope: this },
            dblclick: { fn: this.handleToolbarEditPage, scope: this },
            expandnode: { fn: this.handleNodeExpand, scope: this },
            collapsenode: { fn: this.handleNodeCollapse, scope: this },
            collapsenode: { fn: this.handleNodeCollapse, scope: this }
        });

        Ext.apply(config, {
            autoScroll: true,
            animate: true,
            enableDD: true,
            containerScroll: true,
            border: false,
            root: this.rootNode,
            tbar: [{
                text: 'Add',
                icon: '/core/images/icons/add.png',
                cls: 'x-btn-icon-text',
                scope: this,
                handler: this.handleToolbarAddPage
            }
            , {
                text: 'Edit',
                icon: '/core/images/icons/edit.png',
                cls: 'x-btn-icon-text',
                scope: this,
                handler: this.handleToolbarEditPage
            }
            , {
                text: 'Delete',
                icon: '/core/images/icons/cross.png',
                cls: 'x-btn-icon-text',
                scope: this,
                handler: this.handleToolbarDeletePage
            }
            /*, "->"
            , {
                title: 'View',
                icon: '/core/images/icons/page_white_magnify.png',
                cls: 'x-btn-icon',
                scope: this,
                handler: this.handleToolbarViewPage
            }*/]
        });

        // call parent contructor
        Tg.PageTree.superclass.constructor.apply(this, arguments);

        this.on('startdrag', this.handleNodeStartDrag, this);
        this.on('movenode', this.handleNodeMove, this);
        this.on('afterrender', this.handleAfterRender, this);

        var url = document.location.href;
        if (url.indexOf('#') > 0) {
            this.currentNodeId = url.substring(url.indexOf('#') + 1);
        }

        if (!this.currentNodeId && $.cookie('tgPageTree_current'))
            this.currentNodeId = $.cookie('tgPageTree_current');

        Tg.PageFactory.pageTree = this;

        if (!this.currentNodeId)
            this.currentNodeId = Tg.PageFactory.pages.id;
    }

	, findNode: function (id) {
	    return this.getNodeById(id + "");
	}

    , initComponent: function () {
        var config = {};

        Ext.apply(this, config);
        Ext.apply(this.initialConfig, config);

        Tg.PageTree.superclass.initComponent.apply(this, arguments);
    }

	, createTreeNode: function (json) {
	    var config = {
	        id: json['id'] + "",
	        text: json['title'],
	        allowChildren: true,
	        expanded: this.expandedNodes.indexOf(json['id'] + "") > -1 ? true : false,
	        pages: []
	    };
	    Ext.applyIf(config, json);

	    var root = new Ext.tree.TreeNode(config);

	    if (json['Pages'])
	        json['pages'] = json['Pages'];

	    if (!json['themeId'])
	        json['themeId'] = 1;

	    for (var i = 0; i < json['pages'].length; i++) {
	        var sub = this.createTreeNode(json['pages'][i]);
	        root.appendChild(sub);
	    }

	    return root;
	}

    , handleNodeStartDrag: function (tree, node, event) {
        oldPosition = node.parentNode.indexOf(node);
        oldNextSibling = node.nextSibling;
    }

    , handleNodeMove: function (tree, node, oldParent, newParent, position) {
        var previousSiblingId = node.previousSibling != null ? node.previousSibling.id : ''; // if no previous sibling move to the first child

        var url = Tg.Config.PageFactory.urlMovePage;

        var params = { 'pageId': node.id, 'parentId': newParent.id, 'previousSiblingId': previousSiblingId };

        //         we disable tree interaction until we've heard a response from the server
        //         this prevents concurrent requests which could yield unusual results

        //        tree.disable();

        Ext.Ajax.request({
            url: url,
            params: params,
            success: function (response, request) {

                // if the first char of our response is not 1, then we fail the operation,
                // otherwise we re-enable the tree

                //                if (response.responseText.charAt(0) != 1) {
                //                    request.failure();
                //                } else {
                tree.enable();
                //                }
            },
            failure: function () {

                // we move the node back to where it was beforehand and
                // we suspendEvents() so that we don't get stuck in a possible infinite loop

                tree.suspendEvents();
                oldParent.appendChild(node);
                if (oldNextSibling) {
                    oldParent.insertBefore(node, oldNextSibling);
                }

                tree.resumeEvents();
                tree.enable();

                alert("Oh no! Your changes could not be saved!");
            }
        });
    }

    , handleAfterRender: function () 
    {
        this.currentNode = this.findNode(this.currentNodeId);

        if (!this.currentNode)
            this.currentNode = this.getRootNode();

        this.currentNode.select();
    }

    , handleNodeClick: function (n) 
    {
    	n.expand();
        this.currentNode = n;
        this.currentNodeId = n.attributes.id;
        $.cookie('tgPageTree_current', this.currentNodeId, { expires: 7, path: '/' });

        document.location.hash = "#" + this.currentNodeId;
    }

    , handleNodeExpand: function (n) {
        var pageId = n.attributes.id + "";
        var pos = this.expandedNodes.indexOf(pageId)
        if (pos == -1)
            this.expandedNodes.push(pageId);
        $.cookie('tgPageTree_expanded', this.expandedNodes, { expires: 7, path: '/' });
    }

    , handleNodeCollapse: function (n) {
        var pageId = n.attributes.id + "";
        var pos = this.expandedNodes.indexOf(pageId)
        while (pos > -1) {
            this.expandedNodes.splice(pos, 1);
            pos = this.expandedNodes.indexOf(pageId)
        }
        $.cookie('tgPageTree_expanded', this.expandedNodes, { expires: 7, path: '/' });
    }

    , handleToolbarAddPage: function () {
        Tg.PageFactory.addPage(this.currentNode.attributes.id);
    }

    , handleToolbarEditPage: function (n) {
        if (n instanceof Ext.tree.TreePanel)
            this.currentNode = n;
        Tg.PageFactory.editPage(this.currentNode.attributes.id);
    }

    , handleToolbarDeletePage: function () {
        Tg.PageFactory.deletePage(this.currentNode.attributes.id);
    }

    , handleToolbarViewPage: function () {
        //TODO - change to loop through name attr of parents and this and dynamically created URL
        window.open(this.currentNode.attributes.url, "tgPreview");
    }
}); 
 
// register xtype
Ext.reg('Tg.PageTree', Tg.PageTree); 
 
// eof