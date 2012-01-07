// A new generic text field
var textField = new Ext.form.TextField();

//Ext.lib.Ajax.defaultPostHeader = 'application/json'
Ext.Ajax.defaultHeaders = ({ "Content-Type": "application/json; charset=utf-8;" });

Tg.TemplatesFactory = Ext.extend(Ext.util.Observable, {
    store: null

	, constructor: function () {
	    var api = {
	        read: '/Admin/Site/TemplatesRead',
	        create: '/Admin/Site/TemplateCreate',
	        update: '/Admin/Site/TemplateUpdate',
	        destroy: '/Admin/Site/TemplateDelete'
	    };

	    var proxy = new Ext.data.HttpProxy({
	        api: api
	    });

	    var reader = new Ext.data.JsonReader({
	        totalProperty: 'total',
	        successProperty: 'success',
	        idProperty: 'id',
	        root: 'data',
	        messageProperty: 'message' 
	    }, [
                { name: 'id' },
                { name: 'name', allowBlank: false },
                { name: 'controller', allowBlank: false },
                { name: 'action', allowBlank: false }
            ]);

	    var writer = new Ext.data.JsonWriter({
	        encode: false,
	        writeAllFields: true
	    });

	    this.store = new Ext.data.Store({
	        id: 'template',
	        proxy: proxy,
	        reader: reader,
	        writer: writer,  
	        autoSave: true // <-- false would delay executing create, update, destroy requests until specifically told to do so with some [save] buton.
	    });

	    // load the store immeditately
	    this.store.load();
	}
});