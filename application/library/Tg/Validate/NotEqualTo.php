<?php
/**
 * Identical Validator
 *
 * @copyright  Copyright (c) 2008 Delete Limited (http://www.deletelondon.com)
 */

class Tg_Validate_NotEqualTo extends Zend_Validate_Abstract
{
    const EQUAL_TO = 'equalTo';

    protected $_messageTemplates = array(
        self::EQUAL_TO => 'Equal to'
    );

    protected $_valueToCheck;

	public function __construct($_valueToCheck)
	{
		$this->_valueToCheck = (string)$_valueToCheck;
	}

    public function isValid($value, $context = null)
    {
        $value = (string) $value;
        $this->_setValue($value);

        if ($value != $this->_valueToCheck) {
        	return true;
        }

        $this->_error(self::EQUAL_TO);
        return false;
    }
}