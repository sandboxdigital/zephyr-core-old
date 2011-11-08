(function(){	
	var defaults  = {
			editModeUrl:"/admin/content/edit-mode",
			editMode:false
	};
	
	function CMSPanelController (options) {
		this.init (options);
	};
	
	$.extend(CMSPanelController.prototype, {
		page : null,
		editMode : false,
		visible: false,
		hideInt : 0,
		
		init : function (options) {
		},
		
		definePage : function (page, editMode) {
			this.page = page;
			this.editMode = editMode?true:false;
		},
				
		editPageProperties : function () {
			var data = {
				"id":this.page.id,
				'showSubPages':false
				};			
			$.get (
				"/admin/site/page-edit-properties", 
				data,
				$.inScope(this.onPagePropsLoad,this));
			return false;
		},
		
		onPagePropsLoad : function (data) {
			this.cmsDialog.dialog("open");
			this.cmsDialog.html(data);
		},
				
		editPageContent : function () {
			this.cmsDialog.dialog("open");
			this.cmsDialog.html('<div id="contentPanelBody"></div>');

		    var data = {
				"contentTemplateId":1,
				"contentId":"SitePage"+this.page.id
				};
		        
		    this.load (data);
			
			return false;
		}

	    , createForm: function () {
	        if (this.form != null)
	            return;

	        var formOptions = {
	            'url': '/admin/content/save'
	            , 'subform': false
	            , 'hideSave': false
	            , 'container': '#contentPanelBody'
	            , 'onSave': jQuery.inScope(this.onSave, this)
	        };
	        form = new CMS.Form(formOptions);

	        this.form = form;
	    }
	    , load: function (data, version) {
	        version = version || 0;

	        this.createForm();

	        Ext.apply(data, { version: version });

	        $.ajax({
	            url: '/admin/content/load',
	            dataType: 'json',
	            data: data,
	            success: jQuery.inScope(this.onLoad, this)
	        });
	    }
	    
	    , onLoad : function (reponse)
	    {
	    	if (reponse.success) {
		    	this.form.setOptions(reponse.data.options);
		    	this.form.loadXml (reponse.data.formXml);
		    	this.form.populateXml (reponse.data.dataXml);
	    	} else
	    	{
	    		this.form.clear ();
	    	}    	
	    }
	    
	    ,onSave : function (response)
	    {
	    	c(response);
	    }
		
		,onPageContentLoad : function (data) {
			this.cmsDialog.dialog("open");
			this.cmsDialog.html(data);
		},
		
		onDialogCancelClick : function (ev) {
			this.cmsDialog.dialog("close");
		},
		
		toggle : function () 
		{
			if (this.visible)
				this.hide ();
			else
				this.show();
			
			return false;
		},
		
		show : function () 
		{
			clearTimeout (this.hideInt);
			
			$('#CMSPagePanel').stop();
			$('#CMSPagePanel').animate({'top':0},250);
			$('#CMSToggleVisible').removeClass('hidden');
			$.cookie ('panelVisible', "true", {expires: 7, path: '/'});
			this.visible = true;
		}, 
		
		hide : function () 
		{
			this.hideInt = setTimeout($.inScope(this.hideTM,this),500);
		},
		
		hideTM : function () 
		{
			$('#CMSPagePanel').stop();
			$('#CMSPagePanel').animate({'top':-33},250);
			$('#CMSToggleVisible').addClass('hidden');
			$.cookie ('panelVisible', "false", {expires: 7, path: '/'});
			this.visible = false;
		},
		
		addButton : function (title, url) {
			var a = $('<a href="#"></a>').attr("href","#").html(title);
			
			$('#CMSPagePanelButtons').append(a);
		},
				
					
		onReady : function () {	
			if (this.page) {
				$(document).css({"margin-top":"30px"});
				
				// page panel
				var div = '<div id="CMSPagePanel" style="top:-33px;"><table border="0" cellpadding="0" cellspacing="0" style="width:100%;height:45px;"><tr><td class="tleft"></td>'+
				'<td class="tcontent">'+
				'<div style="height:33px;" id="CMSPagePanelButtons">'+
				'<a id="CMSPanelEditProp" class="editMode" href="#" onclick="$.cmsPanel.editPageProperties();return false;">Edit page properties</a><a id="CMSPanelEditContent" class="pageProps" href="#" onclick="$.cmsPanel.editPageContent();return false;">Edit page content</a>'+
				'<a id="CMSPagePanelSwitch" class="admin" href="/admin/site/pages?page='+this.page.url+'" target="admin-page">Goto admin</a>'+
				'</div>'+
				'<div style="margin:0px auto;width:40px;height:12px;"><a href="#" id="CMSToggleVisible" class="hidden">-</a></div>'+
				'</td><td class="tright"></td></tr></table></div>';	
				
				this.cmsPanel = $(div)
				.appendTo('body')
				.width($(document).width());
	
				var panelVisible = $.cookie ('panelVisible');
				if (panelVisible=="true") {
					$('#CMSPagePanel').css({'top':0});
					$('#CMSToggleVisible').removeClass('hidden');
					this.visible = true;
				}
				
				// edit dialog
				div = '<div id="CMSDialog" title="Form">Loading</div>';			
				this.cmsDialog = $(div)
				.appendTo('body')
				.dialog({
					"autoOpen":false, 
					"position": ['center',50],
					"width":575
					});
				
				$('#CMSPagePanel').hover ($.inScope(this.show,this),$.inScope(this.hide,this));
			}
		}	
	});
	
	$.cmsPanel = new CMSPanelController ();
})();

$(document).ready(function() {
	$.cmsPanel.onReady();
});


