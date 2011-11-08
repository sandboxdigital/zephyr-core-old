<?php
/**
 * Identical Validator
 *
 * @copyright  Copyright (c) 2008 Delete Limited (http://www.deletelondon.com)
 */

class Tg_Validate_ElementValue extends Zend_Validate_Abstract
{
    const NOT_MATCH = 'notMatch';

    protected $_messageTemplates = array(
        self::NOT_MATCH => 'Fields\'s do not match'
    );

	protected $_field;
	protected $_value;

	public function __construct($field, $value)
	{
		$this->_field = $field;
		$this->_value = $value;
	}

    public function isValid($value, $context = null)
    {    	
        $value = (string) $value;
        $this->_setValue($value);

        if (is_array($context)) {
            if (isset($context[$this->_field])
                && ($value == $context[$this->_field])) {
                return true;
            }
        }
		elseif (is_string($context) && ($value == $context)) {
            return true;
        }

        $this->_error(self::NOT_MATCH);
        return false;
    }
}