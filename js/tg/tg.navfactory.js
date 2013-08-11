/*globals Tg,Ext,c
*/

(function (Ext,Tg){
    "use strict";

    // singleton class for managing pages
    // extends Ext.util.Observable

    Tg.Config.NavFactory = {
        urlAddNav: "/admin/nav/save"
        , urlEditNav: "/admin/nav/save"
        , urlDeleteNav: "/admin/nav/delete"
        , urlMoveNav: "/admin/nav/move"
    };

    Tg.NavFactory = Ext.extend(Ext.util.Observable, {
        name: 'barfoo'
        , templates: null
        , themes: null
        , navs: null
        , navTree: null

        , constructor: function () {
           //alert ("hi")
        }

        , setNavs: function (navs) {
           this.navs = navs;
        }

        , findNav: function (nav, itemId) {
           return this._findNav(this.pages, itemId)
        }

        , _findNav: function (node, pageId) {
           if (node.id === pageId) {
               return node;
           }

           for (var i = 0; i < node.pages.length; i++) {
               var returnNode = this._findNav(node.pages[i], pageId);
               if (returnNode !== null) {
                   return returnNode;
               }
           }

           return null;
        }

        , addNav: function (parent) {
            var data = {
                id:0,
                type:1,
                parentId:parent.attributes.id,
                title:Tg.PageFactory.pages.title,
                page:Tg.PageFactory.pages.id
            };
            var node = parent.appendChild ({
                text:data.title,
                attributes:data,
                json:data,
                children:[],
                expanded:false
            });
            node.select();
        }

        , editNavSave: function (form, node) {
             form.submit({
                clientValidation: true,
                url: Tg.Config.NavFactory.urlEditNav,
                params: {
                    newStatus: 'delivered'
                },
                success: function (form, action) {
                    var values = form.getValues();
                    var node = Tg.NavFactory.navTree.getNodeById(values.id);
                    node.setText(values.title);
                    node.attributes.json = values;
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

        , deleteNav: function (id) {
           var _this = this;

            Ext.Msg.confirm(
                'Delete?',
                'Are you sure you want to delete this menu item?',
                function (r) {
                    if (r === 'yes') {
                        var data = { "id": id };
                        $.ajax({ url: Tg.Config.NavFactory.urlDeleteNav, data: data, type: 'POST' });
                        Tg.NavFactory.navTree.getNodeById(id).parentNode.select();
                        Tg.NavFactory.navTree.getNodeById(id).remove();
                    }
                }
            );
        }
    });

    // overwrite
    Tg.NavFactory = new Tg.NavFactory();

    Ext.onReady(function() {
        //Tg.NavFactory.editNav (1);
    });

})(window.Ext,window.Tg);