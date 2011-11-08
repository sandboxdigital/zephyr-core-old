<?php

class Tg_Form_Element_Percent extends Zend_Form_Element_Multi
{
	public $helper = 'formPercent';

	/**
	 * SelectMulti is an array of values by default
	 * @var bool
	 */
	protected $_isArray = true;



	/**
	 * Override isValid()
	 *
	 * Ensure that validation error messages mask password value.
	 *
	 * @param  string $value
	 * @param  mixed $context
	 * @return bool
	 */
	public function isValid($value, $context = null)
	{
		$options = $this->getMultiOptions();
		
		// are they all blank/0?
		$allBlank = true;
		foreach($options as $key => $name)
		{
			if ($value[$key]!='' && $value[$key]!=0)
				$allBlank=false;
		}
	
		$this->setValue ($value);
		
		if ($allBlank)
			return true;

		// set blanks to 0
		$allBlank = true;
		foreach($options as $key => $name)
		{
			if ($value[$key]=='') 
				$value[$key] = 0;
		}
	
		$this->setValue ($value);
			
		$validators = $this->getValidators();

		array_unshift($validators, new Zend_Validate_Float());
		array_unshift($validators, new Zend_Validate_Between(0,100));
		$this->setValidators($validators);

		// set to 0 if empty
		foreach($options as $key => $name)
		{
			if (empty($value[$key]))
				$value[$key] = 0;
		}

		$this->setValue ($value);

		$valid = parent::isValid($value, $context);

		if (!$valid)
			return false;
		
		// figure out total
		$total = 0;
		foreach($options as $key => $name)
		{
			$total += $value[$key];
		}
		
		if ($total > 100) 
		{
			$this->_messages = array('percentTotal'=>$this->getLabel().' must be less than 100');
			$this->_errors   = array('percentTotal');
			$valid = false;
		}

		return $valid;
	}
}