(function(){	
	var defaults  = {
			editModeUrl:"/admin/cms/edit-mode",
			editMode:false
	};
	
	function CMSController (options) {
		this.init (options);
	};
	
	$.extend(CMSController.prototype, {
		init : function (options) {
			this.options = $.extend({},defaults, options);
			this.templates = new Array ();
			this.elementId = 0;
			this.blocks = new Array ();
		},
		
		addBlock : function (id, templates) {
			this.blocks[this.blocks.length] = id;
			this.templates = templates;
		},
		
		addMenu : function () {
						
		},
		
		setModeEdit : function () {			
			this.injectCmsPanel($('.CMSElement'));
			
			for (var i=0;i<this.blocks.length;i++) {
				var id = this.blocks[i];
				var block = $('#CMSBlock'+id);
				block.append('<div class="CMSBlockFooter clear"><a href="#" class="CMSBlockAdd CMSIconAdd"">Add</a></div>');
			}
			var bindings = new Array();

			for (var i=0;i<this.templates.length;i++)
				bindings[this.templates[i].id+''] = $.inScope(this.onMenuClick,this);
						
			$(".CMSBlockAdd").contextMenu("CMSAddMenu", {
				"bindings":bindings,
				"menuPosition":"object",
				"event":"click",
				onShowMenu:$.inScope(this.onShowMenu,this)
		    });

			$(".CMSBlock").sortable({
				"axis":"y",
				"handle":".CMSElementDetailsMove",
				"forcePlaceholderSize":true,
				"placeholder":".CMSElementPlaceholder",
				"start":function (ev,ui) {
					$('.CMSElement').unbind();
				},
				"update":$.inScope(this.onDragUpdate,this)						
				});
		},
		
		injectCmsPanel : function (jqItems) {			
			// element rollover panel
			var div = '<div class="CMSElementDetails"><div><nobr><a href="#" class="CMSElementDetailsMove CMSIconMove" title="Move">Move</a><a href="#" class="CMSElementDetailsDelete CMSIconDelete" title="Delete">Delete</a></nobr></div></div>';
			
			jqItems.each(function () {
				var jqItem = $(this);
				var details = jqItem.find(".CMSElementDetails");
				if (details.length==0)
					details = $(div).appendTo(jqItem);	
					
				var w = jqItem.width ();
				var mw = details.width();
				details.css({'left':(w-mw)+'px','top':'1px'}).hide();
			});

			jqItems.unbind ();
			jqItems.hover ($.inScope(this.onElementOver,this), $.inScope(this.onElementOut,this));
			 $('.CMSElementDetailsDelete').click ($.inScope(this.onDeleteClick,this));
		},
		
		onDragUpdate : function (ev, ui) {
			$('.CMSElement').hover ($.inScope(this.onElementOver,this), $.inScope(this.onElementOut,this));
			var blockId = ui.item.attr("blockid");
			var block = $("#CMSBlock"+blockId);
			var s = $(block).sortable("serialize",
				{
				"expression":"CMSElement(.+)",
				"key":"sort[]"
				}				
			);
			$.get ("/admin/cms/element-reorder?blockId="+blockId+"&"+s);
		},
		
		onElementOver : function (event) {
			if (!event.currentTarget)
				return;
			var jqItem = $(event.currentTarget);

			var details = jqItem.find(".CMSElementDetails");
			details.fadeIn();
			
			this.elementId = jqItem.attr('elementid');
		},
		
		onElementOut : function (event) {			
			var jqItem = $(event.currentTarget);
			var details = jqItem.find(".CMSElementDetails");
			details.fadeOut();		
		},
		
		onShowMenu : function (ev, menu) {
			this.blockId = $(ev.currentTarget.parentNode.parentNode).attr("blockid");
			c(this.blockId);
			return menu; // lazy loading - could create the menu now!!!
		},
		
		onMenuClick : function (trigger, currentTarget) {
			var elementTemplateId = currentTarget.id;			
			var blockId = this.blockId; 
			var block = $("#CMSBlock"+blockId);
			var subForm = block.attr("subform");
			var data = {
				"blockId":blockId,
				'editMode':1,
				"elementTemplateId":elementTemplateId,
				'subForm':subForm
				};			
			$.get (
				"/admin/cms/element-create", 
				data,
				$.inScope(this.onCreateLoad,this));
		},
		
		onCreateLoad : function (data) {
			var blockId = this.blockId; 
			var block = $("#CMSBlock"+blockId).find(".CMSBlockFooter").before (data);
			this.injectCmsPanel($('.CMSElement'));
		},
		
		onDeleteClick : function (ev) {
		 	var elementId=this.elementId;
			if (confirm("Are you sure you want to delete this element?")) {
				$("#CMSElement"+elementId).fadeOut ("fast", function () {$(this).remove()});
				$.ajax({
				  url: "/admin/cms/element-delete",
				  cache: false,
				  data: "elementId="+elementId
				});
			}
		 	return false;
		},
		
		onReady : function () {	
			// create add menu
			div = '<div class="contextMenu" id="CMSAddMenu"><ul>';
			for (var i=0;i<this.templates.length;i++) {
				var t = this.templates[i];
				div+= '<li id="'+t.id+'">'+t.name+'</li>';
			}
			div+= '</ul></div>';
			
			this.cmsAddMenu = $(div).appendTo('body');
			
			// odd bug in safari  3.1 - .width() is returning wrong value - maybe because page has not rendered yet?
			setTimeout(this.setModeEdit,this), 20);		
		}	
	});
	
	$.cms = new CMSController ();
})();

$(document).ready(function() {
	$.cms.onReady();

});

tinyMCE.init({
	theme : "advanced",
	mode : "none",
	theme_advanced_buttons1 : "bold,italic,underline,strikethrough,bullist,numlist,|,formatselect,|,link,unlink,image",
	theme_advanced_buttons2 : "undo,redo,|,cut,copy,paste,|,code,cleanup,removeformat",
	theme_advanced_buttons3 : "",
	theme_advanced_toolbar_location : "top",
	theme_advanced_toolbar_align : "left",
	theme_advanced_statusbar_location : "bottom",
	theme_advanced_resizing : true,
	file_browser_callback : "myFileBrowser"			
});
