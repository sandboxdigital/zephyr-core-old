<?php
class Tg_View_Helper_SiteBreadcrumb
{
	private $_defaults = array (
		'includeCurrent'=>true,
		'includeRoot'=>false,
		'seperator'=>'&nbsp;|&nbsp;',
		'aNormalClass'=>'',
		'urlPre'=>''
	);
	
	
	function siteBreadcrumb (array $options = null, Tg_Site_Db_Page $Page = null) {
		/// maybe this should check to see if the Controller is a Tg_Site_Controller and return nothing if it's not
		//$controller = Zend_Controller_Front::getInstance()->get		
		$Pm = Tg_Site::getInstance();
		if ($Page === null)
			$Page = $Pm->getCurrentPage();
			
		if (null === $options)
			$options = $this->_defaults;
		else
			$options = $options + $this->_defaults;	
		
			
		$Ancestors = $Page->getAncestors();
		if (!$options['includeCurrent'])
			array_pop($Ancestors);
		if (!$options['includeRoot'])
			array_shift($Ancestors);
			
		$return = '';
		for ($i=0;$i<count($Ancestors);$i++) {
			$Ancestor = $Ancestors[$i];
			$aClass = $options['aNormalClass'];
			$return .= '<a href="'.$options['urlPre'].$Ancestor->url.'" class="'.$aClass.'">'.$Ancestor->title.'</a>';
				
			if ($i<count($Ancestors)-1)
				$return .= $options['seperator'];
		}
		return $return;
	}
}