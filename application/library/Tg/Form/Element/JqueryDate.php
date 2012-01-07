<?php

class Tg_Form_Element_JqueryDate extends Zend_Form_Element_Xhtml
{
    public $helper = 'formJqueryDate';

    public function __construct($spec, $options = null)
    {
    	parent::__construct($spec, $options);
    	
        $attribs = $this->getAttribs();
        if (!isset($attribs['format']))        
        	$this->setAttrib('format', 'dd-MM-YYYY');
        else
        	$this->setAttrib('format', $attribs['format']);
    }
    
    /**
     * Set the value
     * @param Zend_Date $value
     */
    public function setValue ($value) {
		if ($value instanceof Zend_Date)
			$this->_value = $value;
		elseif ($value == null || $value == '' || $value=='0000-00-00' || $value=='0000-00-00 00:00:00')
			$this->_value = null;			
		else {
			// try and convert to a date
			$date = null;
			try {
				$attribs = $this->getAttribs();
			    $format = $attribs['format'];
			   	$date = new Zend_Date($value, $format); 
			} catch (Exception $e) {}
	
			$this->_value = $date;
		}
    }

	public function isValid($value, $context = null)
	{
		$date = null;
		if ($value != null) {
			try {
				// try and convert to a date
				$attribs = $this->getAttribs();
			    $format = $attribs['format'];
			   	$date = new Zend_Date($value, $format);
			} catch (Exception $e) {}
		}

		$this->setValue($date);
		
		if ($this->_required && $date==null) {
			$this->_errors[]='dateInvalid';
			$this->_errorMessages[]='dateInvalid';
			return parent::isValid($value);
		} else
			return true;
	}
}