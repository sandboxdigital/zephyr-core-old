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
            parent.appendChild ({
                text:data.title,
                attributes:data,
                json:data,
                children:[],
                expanded:false
            });
        }

//        , editNav: function (pageId) {
//           var page = this.findNav(pageId);
//           var form = this._getForm();
//           form.form.setValues(page);
//
//           var win = new Tg.FormWindow(form);
//           win.url = Tg.Config.NavFactory.urlEditNav;
//           win.show();
//           win.focus();
//           win.on('save', function (formPanel, values, results) {
//                // TODO: move to a page.update() method
//               var node = Tg.NavFactory.navTree.getNodeById(pageId);
//               node.setText(values.title);
//
//               for (var property in values) {
//                   Tg.NavFactory.navTree.getNodeById(pageId).attributes[property] = values[property];
//
//                   page[property] = values[property];
//               }
//           });
//        }

        , editNavSave: function (form) {
             // TODO - update the page json model
             form.submit({
                clientValidation: true,
                url: Tg.Config.NavFactory.urlEditNav,
                params: {
                    newStatus: 'delivered'
                },
                success: function (form, action) {
                    // TODO: move to a page.update() method
                    var values = form.getValues();
                    var page = Tg.NavFactory.findNav(values.id);
                  var node = Tg.NavFactory.navTree.getNodeById(values.id);
                  node.setText(values.title);
                  for (var property in values) {
                      Tg.NavFactory.navTree.getNodeById(values.id).attributes[property] = values[property];
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

        , deleteNav: function (pageId) {
           var page = this.findNav(pageId);
           var _this = this;

           Ext.Msg.confirm(
                'Delete?',
                'Are you sure you want to delete this page?',
                function (r) {
                 if (r == 'yes') {
                     var data = { "id": pageId };
                     $.ajax({ url: Tg.Config.NavFactory.urlDeleteNav, data: data, type: 'POST' });
                     Tg.NavFactory.navTree.getNodeById(pageId).parentNode.select();
                     Tg.NavFactory.navTree.getNodeById(pageId).remove();
                 }
                });
        }
    });

    // overwrite
    Tg.NavFactory = new Tg.NavFactory();

    Ext.onReady(function() {
        //Tg.NavFactory.editNav (1);
    });

})(window.Ext,window.Tg);