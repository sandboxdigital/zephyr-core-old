<?php
class Tg_View_Helper_SiteUrl
{
	private $_defaults = array (
		'showParent'			=>false,
		'showAll'				=>false,
		'aNormalClass'			=>'',
		'aSelectedClass'		=>'selected',
		'aSelectedParentClass'	=>'selectedParent',
		'liNormalClass'			=>'',
		'liSelectedClass'		=>'selected',
		'liSelectedParentClass'	=>'selectedParent',
		'pruneRestricted'		=>false,
		'aRestrictedClass'		=>'restricted',
		'liRestrictedClass'		=>'restricted',
		'depth'					=>100					// 100 - unlimited
	);
		
	function siteNav (array $options = null, $PmPage = null) {
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
			
		if ($options['depth']<=0)
			return '';
		
		$options['depth']--;
			
		$return = '';
		if (count($PmPage->Pages)>0) {
			
			// get visible pages
			$pages=array();
			
			if ($options['showParent'])
				$pages[]=$PmPage;
			
			foreach ($PmPage->Pages as $subPage){
				if ($subPage->visible) {
					$pages[]=$subPage;
				}
			}
			
			// create html
			$return .= '<ul>';
			
//			if ($options['showParent'])
//			{
//				$return .= '<li class="'.$liClass.'" id="'.$PmPage->name.'"><a href="'.$PmPage->url.'" class="'.$aClass.'">'.$PmPage->title.'</a>';
//				$return .= '</li>';
//			}
			$user = Tg_Auth::getAuthenticatedUser();
			
			foreach ($pages as $subPage) {
				$allowed = Tg_Site_Acl::isUserAllowed($user, $subPage->path);
				
				if ($allowed || !$options['pruneRestricted']) {
					if ($CurrentPage->path==$subPage->path) {
						$liClass = $options['liSelectedClass'];
						$aClass = $options['aSelectedClass'];
					} else if ($subPage->isAncestor($CurrentPage)) {
						$liClass = $options['liSelectedParentClass'];
						$aClass = $options['aSelectedParentClass'];
					} else {
						$liClass = $options['liNormalClass'];
						$aClass = $options['aNormalClass'];
					}
							
					if (!$allowed) {
						$liClass .= ' '.$options['liRestrictedClass'];
						$aClass .= ' '.$options['aRestrictedClass'];
					}				
					if ($subPage==$pages[0]) {
						$liClass .= ' first';
						$first = false;
					}
					if ($subPage==$pages[count($pages)-1]) {
						$liClass .= ' last';
					}
					$return .= '<li class="'.$liClass.'" id="'.$subPage->name.'"><a href="'.$subPage->url.'" class="'.$aClass.'">'.$subPage->title.'</a>';
					if ($subPage->isAncestor($CurrentPage) || $options['showAll'])
						$return .= $this->siteNav($subPage, $options);
					$return .= '</li>';
				}
			}
			$return .= '</ul>';
		}
		return $return;
	}
}