<?php
class Tg_View_Helper_Nav
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
		
	function nav ($navItem = 'Main', array $options = null) {
        if (is_string($navItem)) {
            $nav = Tg_Nav::getNav($navItem);
            $navItem = $nav->getRootNavitem();
        }


		if (null === $options)
			$options = $this->_defaults;
		else
			$options = $options + $this->_defaults;		

		$Pm = Tg_Site::getInstance();
		$CurrentPage = $Pm->getCurrentPage();

		if (!$CurrentPage)
			$CurrentPage = $Pm->RootPage; // default to root page 
			
		if ($options['depth']<=0)
			return '';
		
		$options['depth']--;
			
		$return = '';
        $subNavitems=$navItem ->getNavitems();
		if (count($subNavitems)>0) {

			// create html
			$return .= '<ul>';

			foreach ($subNavitems as $subNavitem) {
//
//                if ($CurrentPage->path == $subNavitem->path) {
//                    $liClass = $options['liSelectedClass'];
//                    $aClass = $options['aSelectedClass'];
//                } else if ($subNavitem->isAncestor($CurrentPage)) {
//                    $liClass = $options['liSelectedParentClass'];
//                    $aClass = $options['aSelectedParentClass'];
//                } else {
//                    $liClass = $options['liNormalClass'];
//                    $aClass = $options['aNormalClass'];
//                }


                $liClass = '';
                $aClass = '';

                if ($subNavitem==$subNavitems[0]) {
                    $liClass .= ' first';
                    $first = false;
                }
                if ($subNavitem==$subNavitems[count($subNavitems)-1]) {
                    $liClass .= ' last';
                }

                if ($subNavitem->type == 1) {
                    $page = Tg_Site::getInstance()->getPageById($subNavitem->page);
                    $url = $page?$page->getPath():'';
                } else {
                    $url = $subNavitem->url;
                }

                $title = $subNavitem->title;

                if(isset($options['translate']) && $options['translate'] == true)
                {
                    $t = Zend_Registry::get('translator');
                    if ($t)
                        $title = $t->_($title);
                }

                $return .= '<li class="'.$liClass.'" id="'.$subNavitem->title.'"><a href="'.$url.'" class="'.$aClass.'">'.$title.'</a>';
//                if ($subNavitem != $RootNavitem && ($subNavitem->isAncestor($CurrentPage) || $options['showAll']))
//                {
//                    $options['showParent'] = false;
                    $return .= $this->nav($subNavitem, $options);
//                }
                $return .= '</li>';
            }
			$return .= '</ul>';
		}
		return $return;
	}
}