<?php

class Tg_Form_Element_Content extends Zend_Form_Element_Xhtml
{
    public $helper = 'formContent';

	public function isValid($value, $context = null)
	{
		if (isset($value)) {
//    		$dataXml = Tg_Content::saveXml($_REQUEST['cmsForm'], $this->getAttrib('form'));
			// xml sent in post
			$value = stripslashes($value); 
			$this->setValue('<?xml version="1.0"?>'.$value);
		} else
			$this->setValue('<?xml version="1.0"?><data></data>');
		
		return true;
	}
}