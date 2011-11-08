<?php
require_once 'Zend/Validate/Date.php';

class Tg_Validate_DateInFuture extends Zend_Validate_Date
{
    const DATE_NO_VALID    = 'dateNotInFuture';
    
    /**
     * Validation failure message template definitions
     *
     * @var array
     */
    protected $_messageTemplates = array(
        self::DATE_NO_VALID => "'%value%' is not in the future"
    );
    
    public function isValid($value)
    {
		if (parent::isValid($value))
		{
			// TODO - add test for format and locale == null
			$now = Zend_Date::now();
			$date = new Zend_Date ($value, $this->_format, $this->_locale);
			$date->setDay($now->get('d'));
			$date->setMinute($now->get('m'));
			$date->setSecond($now->get('s'));
			$date->setHour($now->get('h'));
//			echo $date;
//			die;
			if ($date->isEarlier($now)){
				$this->_error(self::DATE_NO_VALID);
				return false;				
			}
			
			return true;
			
		} else 
			return false;
    }
}
