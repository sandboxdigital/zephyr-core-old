<?php

class Tg_Form_Element_Uploadify extends Zend_Form_Element_Xhtml
{

    public $helper = 'formUploadify';

//	public function isValid($value, $context = null)
//	{
//		$key = $this->getName();
//		$fileValue = $value;
//		
//		if ($fileValue == null) {
//			$fieldName = 'hidden_'.$key;
//			
//			if (isset($_REQUEST[$fieldName])) {
//				$fileValue = $_REQUEST[$fieldName];
//			}
//		}	
//		
//		// delete file
//		$fieldName = $key.'_delete';
//		if (isset($_REQUEST[$fieldName])) {
//			if ($fileValue) {
//				$file = Tg_File::getFile ($fileValue);
//				$file->delete();
//			}
//			$fileValue = null;
//		}		
//		
//		$return = parent::isValid($fileValue, $context);
//		
//		$this->setValue($fileValue);
//		
//		return $return;
//	}
}