<?php
class Tg_View_Helper_SiteContent extends Zend_View_Helper_Abstract
{
	public function siteContent ($contentPathId, $content = null)
	{
		$lang = Tg_Site::language();
		
		if (!empty($lang))
			$contentPath = $lang.'/'.$contentPathId;
		else		
			$contentPath = $contentPathId;
			
		// TODO - rewrite to use a 'content stack' an array of content paths to try, 
		// loop over paths and test to see if they have content.
		if (empty($content))
			$content = Tg_Site::getInstance()->getCurrentPage()->getContent();			
		
		$element = $content->getElement($contentPath);
		
		if ($element instanceof Tg_Content_Placeholder || $element instanceof Tg_Content_Element_Group && count($element)==0)
		{	
			// element is empty ... check template for default content
			$templateContent = Tg_Site::getInstance()->getCurrentPage()->getTemplate()->getContent();			
			$element = $templateContent->getElement($contentPath);
			
			if ($element instanceof Tg_Content_Placeholder || ($element instanceof Tg_Content_Element_Group && count($element)==0))
			{
				// template content empty ... default to english
				if (!empty($lang))
					$contentPath = 'en/'.$contentPathId;
					
				$element = $content->getElement($contentPath);
				if ($element instanceof Tg_Content_Placeholder || ($element instanceof Tg_Content_Element_Group && count($element)==0))
				{
					$element = $templateContent->getElement($contentPath);
					return $this->view->contentPartial ($element);
				}
				else
					return $this->view->contentPartial ($element);
			}
			else
				return $this->view->contentPartial ($element);	
		} else 
			return $this->view->contentPartial ($element);		
		
	}
}