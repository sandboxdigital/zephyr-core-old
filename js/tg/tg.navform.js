/*globals Tg,Ext,c
*/

(function (Ext,Tg){
    "use strict";

    Tg.NavForm = Ext.extend(Ext.FormPanel,{
        initComponent:function(data) {
            var types = [
                [1,'Page'],
                [2,'Url']
            ];

            Ext.apply(this,{
                title: 'Menu details',
                frame: true,
                labelWidth: 110,
                bodyStyle: 'padding:0 10px 0;',
                labelAlign: 'right',
                waitMsgTarget: true,
                defaultType: 'textfield'
                , buttons: [{
                    text: 'Save',
                    handler: this.onSubmit,
                    scope: this
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
                            new Ext.form.ComboBox({
                                fieldLabel: 'Type',
                                name: 'type_cb',
                                allowBlank: false,
                                triggerAction: 'all',
                                lazyRender: true,
                                typeAhead: false,
                                mode: 'local',
                                forceSelection: true,
                                store: types,
                                hiddenName: 'type',
                                anchor: '95%'
                            }),
                            new Ext.ux.form.TreeCombo({
                                fieldLabel: 'Page',
                                name: 'page_cb',
                                hiddenName: 'page',
                                root: Tg.PageFactory.pagesToTreeNodes(null, [], true)
                            }),
                            {
                                xtype: 'textfield',
                                fieldLabel: 'URL',
                                name: 'url',
                                anchor: '95%'
                            }
                        ]
                    }]
            });

            Tg.NavForm.superclass.initComponent.call(this, data);
        },

        initActions:function() {
            this.title = this.form.items.get(2);
            this.type = this.form.items.get(3);
            this.page = this.form.items.get(4);
            this.url = this.form.items.get(5);

            this.type.on('select', this.onChange, this);
            this.page.on('select', this.onPageChange, this);
            this.page.on('change', this.onPageChange, this);
        },

        onPageChange : function (id, node)
        {
            this.title.setValue(node.text);
        },

        onChange : function (field, newValue, old)
        {
            this.hideShowType(newValue.json[0]);
        },

        onSubmit : function (a1,a2,a3)
        {
            Tg.NavFactory.editNavSave(this.getForm(), this.node);
        },

        setValues : function (node)
        {
            if (node) {
                c(node.attributes);
                this.node = node;
                this.form.setValues(node.attributes);
                this.hideShowType(node.attributes.type);
            }
        },

        hideShowType : function (type) {
            if (type==='1' || type === 1) {
                // page
                this.url.hide(); // URL
                this.page.show();
            } else {
                // url
                this.url.show();
                this.page.hide();
            }
        }
    });

})(window.Ext,window.Tg);