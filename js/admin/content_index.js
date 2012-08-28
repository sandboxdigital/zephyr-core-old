var contentPanel = null;

Ext.onReady(function() {
	pageTree = new Tg.PageTree ({});
	pageTree.on ('click', loadContent);

    contentPanel = new Tg.ContentPanel ();
    doublePanel = new Tg.DoublePanel (pageTree, contentPanel);
        
	// load content for default page
	loadContent (pageTree.currentNode);
});

function loadContent (pageNode)
{
    contentPanel.load (pageNode);
}

