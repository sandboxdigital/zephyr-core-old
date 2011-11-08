<?php
/**
 * Identical Validator
 *
 * @copyright  Copyright (c) 2008 Delete Limited (http://www.deletelondon.com)
 */

class Tg_Validate_Element extends Zend_Validate_Abstract
{
    const NOT_EQUAL 			= 'elementNotEqual';
    const NOT_GREATER_THAN		= 'elementNotGreaterThan';
    const NOT_LESS_THAN			= 'elementNotLessThan';
    
    const EQUAL					= 1;
    const GREATER_THAN			= 2;
    const LESS_THAN				= 3;

    protected $_messageTemplates = array(
        self::NOT_EQUAL 		=> 'Fields\'s do not match',
        self::NOT_GREATER_THAN	=> 'Fields\'s do not match',
        self::NOT_LESS_THAN 	=> 'Fields\'s do not match'
    );

	protected $_field;
	protected $_matchType;

	public function __construct($field, $matchType = 1)
	{
		$this->_field = $field;
		$this->_matchType = $matchType;
	}

    public function isValid($value, $context = null)
    {
        $value = (string) $value;
        $this->_setValue ($value);
        
        $valueToMatch = null;
        
    	if (is_array($context)) {
            if (isset($context[$this->_field]))
               $valueToMatch = $context[$this->_field];
        }
		elseif (is_string($context)) {
            $valueToMatch = $value;
        }
        
         if ($this->_matchType == self::LESS_THAN) {
	        if ($valueToMatch <= $value) {
	            $this->_error(self::NOT_LESS_THAN);
	            return false;
	        }
         } else if ($this->_matchType == self::GREATER_THAN) {
	        if ($valueToMatch >= $value) {
	            $this->_error(self::NOT_GREATER_THAN);
	            return false;
	        }
         } else {
	        if ($value !== $valueToMatch)  {
	            $this->_error(self::NOT_EQUAL);
	            return false;
	        }
        }
		
		return true;
    }
}