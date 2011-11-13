var contentPanel = null;

Ext.onReady(function() {
	pageTree = new Tg.TemplateGrid({data:Tg.PageFactory.templates});
	pageTree.on ('rowclick', loadContent);

    contentPanel = new Tg.ContentPanel2 ();
    doublePanel = new Tg.DoublePanel (pageTree, contentPanel);
        
	// load content for default page
	loadContent (Tg.PageFactory.templates[0]);
});

function loadContent (grid, index)
{
    var template = Tg.PageFactory.templates[index];

    var data = {
		"contentTemplateId":template.id,
		"contentId":"SitePageTemplate"+template.id
		};
        
    contentPanel.load (data);
}

//example grid
Tg.TemplateGrid = Ext.extend(Ext.grid.GridPanel, {
	initComponent : function(data) {
		var config = {
			tbar : [],
			store : new Ext.data.JsonStore( {
				data : this.initialConfig.data,
				id : 'versions',
				fields : [ 'id', 'name' ]
			}),
			columns : [ {
				header : "Name",
				width : 20,
				sortable : true,
				dataIndex : 'name'
			}],
			viewConfig : {
				forceFit : true
			},
			loadMask : true,
			listeners : {
				rowclick : {
					fn : this.onRowClick,
					scope : this
				}
			}

		}; // eo config object

		// apply config
		Ext.apply(this, Ext.apply(this.initialConfig, config));

		// call parent
		Tg.VersionGrid.superclass.initComponent.apply(this, arguments);

		// load the store at the latest possible moment
		//   this.on({
		//       aftertheme:{scope:this, single:true, fn:function() {
		//        //   this.store.load({params:{start:0, limit:10}});
		//       }}
		//   });

	} // eo function initComponent

	,
	onRowClick : function(grid, rowIndex, e) {
		this.rowIndex = rowIndex;
	}

	,
	onPublish : function() {

	}

	,
	onToolbarEditPage : function() {
		var row = this.getStore().getAt(this.rowIndex);
		this.contentPanel.loadVersion(row.data.version);
	}
});

Ext.reg('templateGrid', Tg.TemplateGrid);