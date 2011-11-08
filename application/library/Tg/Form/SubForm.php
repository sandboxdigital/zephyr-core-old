<?php
class Tg_Form_SubForm extends Zend_Form_SubForm
{
	
	public function __construct($options = array())
	{
		parent::__construct($options);;
		
		$this->addElementPrefixPath('Tg_Validate', 'Tg/Validate/', 'validate');
		$this->addPrefixPath('Tg_Form_Element', 'Tg/Form/Element/', 'element');
		$this->addPrefixPath('Tg_Form_Decorator', 'Tg/Form/Decorator/', 'decorator');
		
		// add translation error array for validation
		$translator = Zend_Registry::get('translator');
		if (isset($translator))
		{
			$this->setTranslator($translator);
		}		
	}
}