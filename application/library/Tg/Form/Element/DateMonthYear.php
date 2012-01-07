<?php

class Tg_Form_Element_DateMonthYear extends Zend_Form_Element_Xhtml
{
    public $helper = 'formDateMonthYear';
    
    private $_format = 'yyyy-MM-dd';

    public function __construct($spec, $options = null)
    {
    	parent::__construct($spec, $options);
    	    	
    	if (isset($options['format']))
    		$this->_format = $options['format'];
    	
    	if (isset($options['inFuture']))
    		$this->addValidator(new Tg_Validate_DateInFuture($this->_format));
    	elseif (isset($options['inPast']))
    		$this->addValidator(new Tg_Validate_DateInPast($this->_format));
    }
    
	public function isValid($value, $context = null)
	{		
		$date = null;
		
		try {
			$value =$value['year'].'-'.$value['month'];
		   	$dateObject = new Zend_Date($value, 'yyyy-MM');
		   	
		   	$date = $dateObject->get($this->_format);
//		   	dump($dateObject->get('yyyy-MM-dd'));
		} catch (Exception $e) {}
		return parent::isValid($date, $context);
	}
}