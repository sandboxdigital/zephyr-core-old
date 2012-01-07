/**
* Tg.DoublePanel
* @extends Ext.Panel
* @author Thomas Garrood
*/

Tg.DoublePanel = Ext.extend(Ext.Panel, {
	options:null,
	defaults : {
		leftTitle:'Pages',
		leftCollapsed: false,
		heightOffset:150,
		container:'content-div'
	},
	
    constructor: function (left, right, options) {
		this.options = $.extend ({}, this.defaults, options || {});	
		
	    if (!this.options.height) {
	    	this.options.height = $(window).height() - this.options.heightOffset;
	    }
	    // Panel for the left
	    var nav = new Ext.Panel({
	        title: this.options.leftTitle,
	        region: 'west',
	        split: true,
	        width: 200,
	        collapsible: true,
	        collapsed: this.options.leftCollapsed,
	        margins: '3 0 3 3',
            layout: 'fit',
	        cmargins: '3 3 3 3'
        , items: [left]
	    });

	    var config = {
	        renderTo: this.options.container,
	        height: this.options.height,
	        layout: 'border',
	        items: [nav, right]
	    };

	    Tg.DoublePanel.superclass.constructor.call(this, config);
	}
}); 