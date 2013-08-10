/*globals Tg,Ext,c
 */

(function (Ext,Tg){
    "use strict";

    var oldPosition = null,oldNextSibling=null;

    Tg.Nav = Ext.extend(Ext.tree.TreeNode, {
        constructor: function (data) {
            data.text = data.title;
            Tg.Nav.superclass.constructor.call(this, data);
        }
    });

    Tg.NavTree = Ext.extend(Ext.tree.TreePanel, {
        border: false,
        expandedNodes: null,
        currentNode: null,
        currentNodeId: null

        , constructor: function (config) {
            if ($.cookie('tgNavTree_expanded')) {
                this.expandedNodes = $.cookie('tgNavTree_expanded').split(",");
            }
            else {
                this.expandedNodes = [];
            }

            var children = this.createNavNodes(Tg.NavFactory.navs);

            // constructor pre-processing
            config = config || {};
            config.listeners = config.listeners || {};
            config.tbar = config.tbar || {};
            Ext.apply(config.listeners, {
                click: { fn: this.handleNodeClick, scope: this },
                dblclick: { fn: this.handleToolbarEditNav, scope: this },
                expandnode: { fn: this.handleNodeExpand, scope: this },
                collapsenode: { fn: this.handleNodeCollapse, scope: this }
            });

            Ext.apply(config, {
                autoScroll: true,
                animate: true,
                enableDD: true,
                containerScroll: true,
                border: false,
                root: {
                    text:'Menus',
                    children:children
                },
                rootVisible:false,
                tbar: [{
                    text: 'Add',
                    icon: '/core/images/icons/add.png',
                    cls: 'x-btn-icon-text',
                    scope: this,
                    handler: this.handleToolbarAddNav
                }
                , {
                    text: 'Delete',
                    icon: '/core/images/icons/cross.png',
                    cls: 'x-btn-icon-text',
                    scope: this,
                    handler: this.handleToolbarDeleteNav
                }
                ]
            });

            // call parent contructor
            Tg.NavTree.superclass.constructor.apply(this, arguments);

            this.on('startdrag', this.handleNodeStartDrag, this);
            this.on('movenode', this.handleNodeMove, this);
            this.on('afterrender', this.handleAfterRender, this);

            var url = document.location.href;
            if (url.indexOf('#') > 0) {
                this.currentNodeId = url.substring(url.indexOf('#') + 1);
            }

            if (!this.currentNodeId && $.cookie('tgNavTree_current')) {
                this.currentNodeId = $.cookie('tgNavTree_current');
            }

            Tg.NavFactory.navTree = this;

            if (!this.currentNodeId) {
                this.currentNodeId = Tg.NavFactory.navs.id;
            }
        }

        , findNode: function (id) {
            return this.getNodeById(id + "");
        }

        , initComponent: function () {
            var config = {};

            Ext.apply(this, config);
            Ext.apply(this.initialConfig, config);

            Tg.NavTree.superclass.initComponent.apply(this, arguments);
        }

        , createNavNodes: function (navs) {
            var navNodes = [];
            var that = this;

            $(navs).each(function(){
                navNodes.push(that.createNavNode(this));
            });

            return navNodes;
        }

        , createNavNode: function (json) {
            var config = {
                id: json.items.id + "",
                text: json.title?json.title:json.name,
                json:json,
                allowChildren: true,
                expanded: this.expandedNodes.indexOf(json.id + "") > -1 ? true : false,
                children: []
            };
            // json is a Nav object, hide the ROOT navitem single item
            for (var i = 0; i < json.items.items.length; i++) {
                config.children.push(this.createNavitemNode(json.items.items[i],config));
            }

            return config;
        }

        , createNavitemNode: function (json, nav) {
            var config = {
                id: json.id + "",
                text: json.title?json.title:json.name,
                json:json,
    //            nav:nav,
                allowChildren: true,
                expanded: this.expandedNodes.indexOf(json.id + "") > -1 ? true : false,
                children: []
            };
            for (var i = 0; i < json.items.length; i++) {
                config.children.push(this.createNavitemNode(json.items[i]));
            }

            return config;
        }

        , handleNodeStartDrag: function (tree, node, event) {
            oldPosition = node.parentNode.indexOf(node);
            oldNextSibling = node.nextSibling;
        }

        , handleNodeMove: function (tree, node, oldParent, newParent, position) {
            var previousSiblingId = node.previousSibling !== null ? node.previousSibling.id : ''; // if no previous sibling move to the first child

            var url = Tg.Config.NavFactory.urlMoveNav;

            var params = { 'navId': node.id, 'parentId': newParent.id, 'previousSiblingId': previousSiblingId };

            //         we disable tree interaction until we've heard a response from the server
            //         this prevents concurrent requests which could yield unusual results

            //        tree.disable();

            Ext.Ajax.request({
                url: url,
                params: params,
                success: function (response, request) {
                    var json = Ext.util.JSON.decode(response.responseText);

                    if (json.success === false)
                    {
                        tree.suspendEvents();
                        oldParent.appendChild(node);
                        if (oldNextSibling) {
                            oldParent.insertBefore(node, oldNextSibling);
                        }

                        tree.resumeEvents();

                        Ext.Msg.alert('Failure', json.msg);
                    }

                    tree.enable();
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

            if (!this.currentNode) {
                this.currentNode = this.getRootNode();
            }

            this.currentNode.select();
        }

        , handleNodeClick: function (n)
        {
            n.expand();
            this.currentNode = n;
            this.currentNodeId = n.attributes.id;
            $.cookie('tgNavTree_current', this.currentNodeId, { expires: 7, path: '/' });

            document.location.hash = "#" + this.currentNodeId;
        }

        , handleNodeExpand: function (n) {
            var navId = n.attributes.id + "";
            var pos = this.expandedNodes.indexOf(navId);
            if (pos === -1) {
                this.expandedNodes.push(navId);
            }
            $.cookie('tgNavTree_expanded', this.expandedNodes, { expires: 7, path: '/' });
        }

        , handleNodeCollapse: function (n) {
            var navId = n.attributes.id + "";
            var pos = this.expandedNodes.indexOf(navId);
            while (pos > -1) {
                this.expandedNodes.splice(pos, 1);
                pos = this.expandedNodes.indexOf(navId);
            }
            $.cookie('tgNavTree_expanded', this.expandedNodes, { expires: 7, path: '/' });
        }

        , handleToolbarAddNav: function () {
            Tg.NavFactory.addNav(this.currentNode);
        }

        , handleToolbarDeleteNav: function () {
            Tg.NavFactory.deleteNav(this.currentNode.attributes.id);
        }
    });

    // register xtype
    Ext.reg('Tg.NavTree', Tg.NavTree);
})(window.Ext,window.Tg);
 
// eof