var contentPanel = null;

Ext.onReady(function() {
	pageTree = new Tg.PageTree ({});
	pageTree.on ('click', loadContent);

    contentPanel = new Tg.ContentPanel2 ();
    doublePanel = new Tg.DoublePanel (pageTree, contentPanel);
        
	// load content for default page
	loadContent (pageTree.currentNode);
});

function loadContent (pageNode)
{
    var template = Tg.PageFactory.findTemplate (pageNode.attributes.templateId);

    var data = {
		"contentTemplateId":template.contentTemplateId,
		"contentId":"SitePage"+pageNode.attributes.id
		};
        
    contentPanel.load (data);
}

