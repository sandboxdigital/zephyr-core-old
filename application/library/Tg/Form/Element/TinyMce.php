<?php

class Tg_Form_Element_TinyMce extends Zend_Form_Element_Textarea
{
    public $helper = 'formTinyMce';
    
//    public function __construct($spec, $options = null)
//    {
//    	parent::__construct($spec, $options);
//    	
//        $attribs = $this->getAttribs();
//        if (!isset($attribs['format']))        
//        	$this->setAttrib('format', 'DD-MM-YYYY');
//        else
//        	$this->setAttrib('format', strtoupper($attribs['format']));
//    }
//
//    public function setValue ($value) {
//		if ($value == null)
//			$this->_value = null;
//		elseif ($value instanceof Zend_Date)
//			$this->_value = $value;
//		else {
//			$date = null;
//			$format = $this->getAttrib('format');
//			$zFormat = str_replace('D', 'd', $format);;
//		   	$date = new Zend_Date($value, $zFormat); 
//	
//			$this->_value = $date;
//		}
//    }
//    
//	public function isValid($value, $context = null)
//	{
//		if (isset($value)) {
//			$format = $this->getAttrib('format');
//			$zFormat = str_replace('D', 'd', $format);
//			$value = new Zend_Date ($value, $zFormat);
//		} else
//			$value = null;
//		
//		$this->setValue ($value);
//			
//		return true;
//	}
}