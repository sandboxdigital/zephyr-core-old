<?php

class Tg_Form_DisplayGroup_Simple extends Zend_Form_DisplayGroup
{
	function __construct($name, $loader, $options)
	{
		parent::__construct($name, $loader, $options);
	}
	
    public function loadDefaultDecorators()
    {
        if ($this->loadDefaultDecoratorsIsDisabled()) {
            return;
        }

        $decorators = $this->getDecorators();
        if (empty($decorators)) {
            $this->addDecorator('FormElements')
                 ->addDecorator('HtmlTag', array('tag' => 'dl'))
                 ->addDecorator('DtDdWrapper');
        }
    }
}
?>