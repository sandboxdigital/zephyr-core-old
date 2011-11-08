
CMS.Form.Field.Html = $.inherit(
	CMS.Form.Field,
	{
		__constructor : function(xml, parent) {
			this.__base (xml, parent);
		},
		
		renderField : function() {
			return '<div class="field cmsFormFieldHtml"><textarea type="text" name="' + this.path + '" id="' + this.elPath + '"></textarea></div>';
		},
		
		renderDone : function ()
		{
			this.addControl ();
			CMS.Form.Textareas.push (this);
		},
		
		addControl : function ()
		{
			var width = $('#'+this.elPath).outerWidth()-15;
			
		    var options = {
		    	width:width		    		
		    };		    
		    options = Ext.apply(options, window.tinyMceOptions);
			this.ed = new tinymce.Editor(this.elPath, options);
			this.ed.onBeforeRenderUI.add(function(ed, controlManager) {
				ed.controlManager = new ControlManager(this, ed);
			} .createDelegate(this));

			this.ed.onPostRender.add(function(ed, controlManager) {
				var s = ed.settings;
				
				// Change window manager
				ed.windowManager = new WindowManager({
					editor: this.ed,
					manager: this.manager
				});
//				// Patch css-style for validation body like ExtJS
//				Ext.get(ed.getContentAreaContainer()).addClass('patch-content-body');
//
//				// Event of focused body
//				Ext.Element.fly(s.content_editable ? ed.getBody() : ed.getWin())
//					.on("focus", this.onFocus, this);
//
//				// Event of blur body
//				Ext.Element.fly(s.content_editable ? ed.getBody() : ed.getWin())
//					.on("blur", this.onBlur, this,
//						this.inEditor && Ext.isWindows && Ext.isGecko ? { buffer: 10} : null
//					);
			} .createDelegate(this));
			this.ed.render();
			tinyMCE.add(this.ed);
		},
		
		removeControl : function ()
		{
			this.ed.save();
			this.ed.hide();
			//this.ed.destroy();
			//this.ed = null;
//	    	tinymce.execCommand('mceRemoveControl', false, this.elPath);
			//tinymce.remove(this.elPath);
		},
		
		populate : function (xml) 
		{
			if (xml.firstChild) 
			{
				$('#'+this.elPath).val(xml.firstChild.data);
			}
		}
		
		,setValue : function (value) 
		{
			$('#'+this.elPath).val(value);
			if (tinyMCE.get(this.elPath))
				tinyMCE.get(this.elPath).setContent (value);
		}
	});

/** ----------------------------------------------------------
WindowManager
*/
WindowManager = Ext.extend( tinymce.WindowManager, {

	/** ----------------------------------------------------------
		Config parameters:
		editor - reference to TinyMCE intstance.
		mangager - WindowGroup to use for the popup window. Could be empty.
	*/
	constructor: function( cfg ) {
		WindowManager.superclass.constructor.call(this, cfg.editor);

		// Set window group
		this.manager = cfg.manager;
	},

	/** ----------------------------------------------------------
	*/
	alert: function(txt, cb, s) {
		Ext.MessageBox.alert("", txt, function() {
			if (!Ext.isEmpty(cb)) {
				cb.call(this);
			}
		}, s);
	},

	/** ----------------------------------------------------------
	*/
	confirm: function(txt, cb, s) {
		Ext.MessageBox.confirm("", txt, function(btn) {
			if (!Ext.isEmpty(cb)) {
				cb.call(this, btn == "yes");
			}
		}, s);
	},

	/** ----------------------------------------------------------
	*/
	open: function(s, p) {

		s = s || {};
		p = p || {};

		if (!s.type)
			this.bookmark = this.editor.selection.getBookmark('simple');

		s.width = parseInt(s.width || 320);
		s.height = parseInt(s.height || 240) + (tinymce.isIE ? 8 : 0);
		s.min_width = parseInt(s.min_width || 150);
		s.min_height = parseInt(s.min_height || 100);
		s.max_width = parseInt(s.max_width || 2000);
		s.max_height = parseInt(s.max_height || 2000);
		s.movable = true;
		s.resizable = true;
		p.mce_width = s.width;
		p.mce_height = s.height;
		p.mce_inline = true;

		this.features = s;
		this.params = p;

		var win = new Ext.Window(
		{
			title: s.name,
			width: s.width,
			height: s.height,
			minWidth: s.min_width,
			minHeight: s.min_height,
			resizable: true,
			maximizable: s.maximizable,
			minimizable: s.minimizable,
			modal: true,
			stateful: false,
			constrain: true,
			manager: this.manager,
			layout: "fit",
			items: [
				new Ext.BoxComponent({
					autoEl: {
						tag: 'iframe',
						src: s.url || s.file
					},
					style : 'border-width: 0px;'
				})
			]
		});

		p.mce_window_id = win.getId();

		win.show(null,
			function() {
				if (s.left && s.top)
					win.setPagePosition(s.left, s.top);
				var pos = win.getPosition();
				s.left = pos[0];
				s.top = pos[1];
				this.onOpen.dispatch(this, s, p);
			},
			this
		);

		return win;
	},

	/** ----------------------------------------------------------
	*/
	close: function(win) {

		// Probably not inline
		if (!win.tinyMCEPopup || !win.tinyMCEPopup.id) {
			WindowManager.superclass.close.call(this, win);
			return;
		}

		var w = Ext.getCmp(win.tinyMCEPopup.id);
		if (w) {
			this.onClose.dispatch(this);
			w.close();
		}
	},

	/** ----------------------------------------------------------
	*/
	setTitle: function(win, ti) {

		// Probably not inline
		if (!win.tinyMCEPopup || !win.tinyMCEPopup.id) {
			WindowManager.superclass.setTitle.call(this, win, ti);
			return;
		}

		var w = Ext.getCmp(win.tinyMCEPopup.id);
		if (w) w.setTitle(ti);
	},

	/** ----------------------------------------------------------
	*/
	resizeBy: function(dw, dh, id) {

		var w = Ext.getCmp(id);
		if (w) {
			var size = w.getSize();
			w.setSize(size.width + dw, size.height + dh);
		}
	},

	/** ----------------------------------------------------------
	*/
	focus: function(id) {
		var w = Ext.getCmp(id);
		if (w) w.setActive(true);
	}
	
});

/** ----------------------------------------------------------
ControlManager
*/
ControlManager = Ext.extend( tinymce.ControlManager, {

	// Reference to ExtJS control Ext.ux.TinyMCE.
	control: null,

	/** ----------------------------------------------------------
	*/
	constructor: function(control, ed, s) {
		this.control = control;
		ControlManager.superclass.constructor.call(this, ed, s);
	},

	/** ----------------------------------------------------------
	*/
	createDropMenu: function(id, s) {
		// Call base method
		var res = ControlManager.superclass.createDropMenu.call(this, id, s);

		// Modify returned result
		var orig = res.showMenu;
		res.showMenu = function(x, y, px) {
			orig.call(this, x, y, px);
			Ext.fly('menu_' + this.id).setStyle("z-index", 200001);
		};

		return res;
	},

	/** ----------------------------------------------------------
	*/
	createColorSplitButton: function(id, s) {
		// Call base method
		var res = ControlManager.superclass.createColorSplitButton.call(this, id, s);

		// Modify returned result
		var orig = res.showMenu;
		res.showMenu = function(x, y, px) {
			orig.call(this, x, y, px);
			Ext.fly(this.id + '_menu').setStyle("z-index", 200001);
		};

		return res;
	}
});


