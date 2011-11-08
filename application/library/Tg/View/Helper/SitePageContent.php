<?php
class Tg_View_Helper_SitePageContent extends Zend_View_Helper_Abstract
{
	public function sitePageContent ($contentPath)
	{
		$content = Tg_Site::getInstance()->getCurrentPage()->getContent();			
		$element = $content->getElement($contentPath);

		if ($element instanceof Tg_Content_Element_Group && count($element)==0)
		{	
			// element is empty ... check template for default content
			$content = Tg_Site::getInstance()->getCurrentPage()->getTemplate()->getContent();			
			$element = $content->getElement($contentPath);
			
//			dump($element);	
			echo $this->view->contentPartial ($element);	
		} else 
			echo $this->view->contentPartial ($element);		
		
	}
}