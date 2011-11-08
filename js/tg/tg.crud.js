

////
// ***New*** centralized listening of DataProxy events "beforewrite", "write" and "writeexception"
// upon Ext.data.DataProxy class.  This is handy for centralizing user-feedback messaging into one place rather than
// attaching listenrs to EACH Store.
//
// Listen to all DataProxy beforewrite events
//
//Ext.data.DataProxy.addListener('beforewrite', function (proxy, action) {
//    App.setAlert(App.STATUS_NOTICE, "Before " + action);
//});

////
// all write events
//
//Ext.data.DataProxy.addListener('write', function (proxy, action, result, res, rs) {
//    App.setAlert(true, action + ':' + res.message);
//});

////
// all exception events
//
Ext.data.DataProxy.addListener('exception', function (proxy, type, action, options, res) {
    if (type === 'remote') {
        Ext.Msg.show({
            title: 'REMOTE EXCEPTION',
            msg: res.message,
            icon: Ext.MessageBox.ERROR,
            buttons: Ext.Msg.OK
        });
    }
});

Tg.Crud = Ext.extend(Ext.util.Observable, {
    form: null,
    grid: null,

    constructor: function (config) {
        if (config.formItems == null || config.formItems == undefined)
            alert("Please define config.formItems");

        this.form = new Tg.Crud.Form({
            title: config.name,
            renderTo: 'user-form',
            items: config.formItems
        });

        var _form = this.form;

        this.gird = new Tg.Crud.Grid({
            title: config.name + "s",
            renderTo: 'user-grid',
            store: config.store,
            columns: config.gridColumns,
            selModel: new Ext.grid.RowSelectionModel({ singleSelect: true }),
            form:_form,
            listeners: {
                rowclick: function (g, index, ev) {
                    var rec = g.store.getAt(index);
                    _form.loadRecord(rec);
                },
                destroy: function () {
                    _form.getForm().reset();
                }
            }
        });

        Tg.Crud.superclass.constructor.call(this, config);
    }
});
/*!
* Ext JS Library 3.2.1
* Copyright(c) 2006-2010 Ext JS, Inc.
* licensing@extjs.com
* http://www.extjs.com/license
*/
Ext.ns('App', 'Tg.Crud');
/**
* Tg.Crud.Grid
* A typical EditorGridPanel extension.
*/
Tg.Crud.Grid = Ext.extend(Ext.grid.GridPanel, {
    renderTo: 'user-grid',
    iconCls: 'silk-grid',
    frame: true,
    height: 300,
    width: 390,

    initComponent: function () {

        // typical viewConfig
        this.viewConfig = {
            forceFit: true
        };

        // relay the Store's CRUD events into this grid so these events can be conveniently listened-to in our application-code.
        this.relayEvents(this.store, ['destroy', 'save', 'update']);

        // build toolbars and buttons.
        this.tbar = this.buildTopToolbar();

        // super
        Tg.Crud.Grid.superclass.initComponent.call(this);
    },

    /**
    * buildTopToolbar
    */
    buildTopToolbar: function () {
        return [{
            text: 'Add',
            iconCls: 'silk-add',
            handler: this.onAdd,
            scope: this
        }, '-', {
            text: 'Delete',
            iconCls: 'silk-delete',
            handler: this.onDelete,
            scope: this
        }, '-'];
    },

    /**
    * onAdd
    */
    onAdd: function (btn, ev) {
        var u = new this.store.recordType({});
        this.store.insert(0, u);
        //this.rowClick(0);
        this.getSelectionModel().selectFirstRow();
        var rec = this.store.getAt(0);
        this.form.loadRecord(rec);
    },

    /**
    * onDelete
    */
    onDelete: function (btn, ev) {
        var model = this.getSelectionModel();
        var index = model.getSelected();
        if (!index) {
            return false;
        }
        var rec = this.store.getById(index.id);
        this.store.remove(rec);
        this.form.clearForm();
    }
});     

/**
* @class Tg.Crud.Form
* A typical FormPanel extension
*/
Tg.Crud.Form = Ext.extend(Ext.form.FormPanel, {
    renderTo: 'user-form',
    frame: true,
    labelAlign: 'right',
    frame: true,
    width: 390,
    defaultType: 'textfield',
    defaults: {
        anchor: '100%'
    },

    // private A pointer to the currently loaded record
    record: null,

    /**
    * initComponent
    * @protected
    */
    initComponent: function () {
        // build the form-fields.  Always a good idea to defer form-building to a method so that this class can
        // be over-ridden to provide different form-fields
        //this.items = this.buildForm();

        // build form-buttons
        this.buttons = this.buildUI();

        // super
        Tg.Crud.Form.superclass.initComponent.call(this);
    },

    /**
    * buildUI
    * @private
    */
    buildUI: function () {
        return [{
            text: 'Save',
            iconCls: 'icon-save',
            handler: this.onUpdate,
            scope: this
        }];
    },

    /**
    * loadRecord
    * @param {Record} rec
    */
    loadRecord: function (rec) {
        this.record = rec;
        this.getForm().loadRecord(rec);
    },

    /**
    * onUpdate
    */
    onUpdate: function (btn, ev) {
        if (this.record == null) {
            return;
        }
        if (!this.getForm().isValid()) {
            App.setAlert(false, "Form is invalid.");
            return false;
        }
        this.getForm().updateRecord(this.record);
    },

    /**
    * onCreate
    */
    onCreate: function (btn, ev) {
        if (!this.getForm().isValid()) {
            App.setAlert(false, "Form is invalid");
            return false;
        }
        this.fireEvent('create', this, this.getForm().getValues());
        this.getForm().reset();
    },

    /**
    * onReset
    */
    onReset: function (btn, ev) {
        this.fireEvent('update', this, this.getForm().getValues());
        this.getForm().reset();
    }
});
