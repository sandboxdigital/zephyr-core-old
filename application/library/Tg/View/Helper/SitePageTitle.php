<?php
class Tg_View_Helper_SitePageTitle
{	
	function sitePageTitle ($Page = null) {
		/// maybe this should check to see if the Controller is a Tg_Site_Controller and return nothing if it's not
		//$controller = Zend_Controller_Front::getInstance()->get	
		$Pm = Tg_Site::getInstance();
		if ($Page === null)
			$Page = $Pm->getCurrentPage();
		
		return $Page->title;
	}
}