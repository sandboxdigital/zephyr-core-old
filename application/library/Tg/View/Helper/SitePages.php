<?php
class Tg_View_Helper_SitePages
{
	private $_defaults = array (
		'sep'					=>'&nbsp;|&nbsp;',
		'showAll'				=>false,
		'pruneRestricted'		=>false
	);
		
	function sitePages ($PmPage = null, array $options = null) {
		/// maybe this should check to see if the Controller is a Tg_Site_Controller and return nothing if it's not
		//$controller = Zend_Controller_Front::getInstance()->get	
		if (null === $options)
			$options = $this->_defaults;
		else
			$options = $options + $this->_defaults;		
		
		
		$Pm = Tg_Site::getInstance();
		if ($PmPage === null)
			$PmPage = $Pm->RootPage;
		elseif (is_string($PmPage))
			$PmPage = $Pm->getPage($PmPage,true);
		
		if (!($PmPage instanceof Tg_Site_Db_Page))
			return '';
		
		$CurrentPage = $Pm->getCurrentPage();
		if (!$CurrentPage)
			$CurrentPage = $Pm->RootPage; // default to root page 
			
		$return = '';
		if (count($PmPage->Pages)>0) {
			
			// get visible pages
			$pages=array();
			
			foreach ($PmPage->Pages as $subPage){
				if ($subPage->visible) {
					$pages[]=$subPage;
				}
			}
			
			// create html
//			$return .= '';
			
			$user = Tg_Auth::getAuthenticatedUser();
			
			$pageList = array ();
			
			foreach ($pages as $subPage) {
//				$allowed = Tg_Site_Acl::isUserAllowed($user, $subPage->path);
				
//				if ($allowed || !$options['pruneRestricted']) {
					$pageList[] = '<a href="'.$subPage->url.'">'.$subPage->title.'</a>';
//				}
			}
//			$return .= '</ul>';
			$return = implode ($options['sep'],$pageList);
		}
		return $return;
	}
}