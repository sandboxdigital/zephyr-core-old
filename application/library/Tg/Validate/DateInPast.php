<?php
require_once 'Zend/Validate/Date.php';

class Tg_Validate_DateInPast extends Zend_Validate_Date
{
    const DATE_NO_VALID    = 'dateNotInPast';
    
    /**
     * Validation failure message template definitions
     *
     * @var array
     */
    protected $_messageTemplates = array(
        self::DATE_NO_VALID => "'%value%' is not in the past"
    );
    
    public function isValid($value)
    {
		if (parent::isValid($value))
		{
			// TODO - add test for format and locale == null
			$date = new Zend_Date ($value, $this->_format, $this->_locale);		
			
			if ($date->isLater(Zend_Date::now())){
				$this->_error(self::DATE_NO_VALID);
				return false;				
			}
			
			return true;
			
		} else 
			return false;
    }
}
