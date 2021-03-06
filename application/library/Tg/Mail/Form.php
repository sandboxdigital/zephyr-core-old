<?php
/**
 * Tg_Mail_Form - mails the contents of a form to an email address
 * 
 * Enter description here ...
 * @author Thomas
 *
 */

class Tg_Mail_Form extends Tg_Mail
{
	public function __construct($charset = null) 
	{
		parent::__construct($charset);		
    }

	public function send(Tg_Form $form) 
	{
		$this->template = null;
		
		foreach ($form->getElements() as $element)
		{
			$type = $element->getType();
            $label = $element->getLabel();
			if ($type != 'Zend_Form_Element_Submit' && !empty($label))
				$this->body .= $label.':<br />'.$element->getValue().'<br /><br />';
		}		
				
		parent::send();
	}
}