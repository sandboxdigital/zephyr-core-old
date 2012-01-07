<?php

class Tg_Form_Element_FileUpload extends Zend_Form_Element_Xhtml
{
    public $helper = 'formFileUpload';
    
    public function init ()
    {
    	parent::init();
    	
    	// checks folders exist
    	Tg_File::getInstance();
    }

	public function isValid($value, $context = null)
	{
		$key = $this->getName();
		$fileValue = $value;
		
		if ($fileValue == null) {
			$fieldName = 'hidden_'.$key;
			
			if (isset($_REQUEST[$fieldName])) {
				$fileValue = $_REQUEST[$fieldName];
			}
		}	
		
		// delete file
		$fieldName = $key.'_delete';
		if (isset($_REQUEST[$fieldName])) {
			if ($fileValue) {
				$file = Tg_File::getFileById($fileValue);
				if ($file)
					$file->delete();
			}
			$fileValue = null;
		}		
		
		$return = parent::isValid($fileValue, $context);
		
		$this->setValue($fileValue);
		
		return $return;
	}
	
	public function getFile ()
	{
		$fileId = $this->getValue ();
		return Tg_File::getFileById($fileId);
	}
}