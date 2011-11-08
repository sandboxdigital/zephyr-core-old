<?php
class Tg_View_Helper_ContentPartial extends Zend_View_Helper_Partial
{
	
	/**
	 * Works in much the same way as the partial view helper except 
	 * the partial scripts is defined by the $content->id
	 * 
	 * 
	 * @param $content
	 * @return unknown_type
	 */
    public function contentPartial ($content, $partialsDir = '_content', $usePartialForGroup = false)
    {
    	$xhtml = '';
    	
    	if ($content instanceof Tg_Content_Placeholder) {
    		return $content->__toString();
    	}
    	else if ($content instanceof Tg_Content_Element_Group && !$usePartialForGroup) {
			foreach ($content as $groupOption) {
				$xhtml .= $this->contentPartial ($groupOption);
			}
	    } else {
    	
			if (isset($content)) {
		    	$view = $this->cloneView();
		
		        $view->assign('content', $content);
		
		        $name = $partialsDir . DIRECTORY_SEPARATOR . $content->id.'.phtml';
		
		        $xhtml .= $view->render($name);
			}
	    }
        return $xhtml;
    }
}
