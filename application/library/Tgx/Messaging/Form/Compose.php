<?php
class Tgx_Messaging_Form_Compose extends Tg_Form {

	public function __construct($actionUrl, $options = array())
	{
		parent::__construct($options);
		
		$this->setAction($actionUrl);

		$this->addElement('hidden', 'to', array (
			'decorators'=>array('ViewHelper')
			));

		$this->addElement('static', 'toName', array (
			'label' 	 => 'To',
			'filters' 	 => array('StringTrim'),
			'class' 	 => 'text',
			'required' 	 => true
			));

		$this->addElement('text', 'subject', array (
			'label' 	 => 'Subject',
			'filters' 	 => array('StringTrim'),
			'class' 	 => 'text',
			'required' 	 => true
			));	
		

		$this->addElement('textarea', 'body', array (
			'label' 	 => 'Message',
			'filters' 	 => array('StringTrim'),
			'class' 	 => 'textareaSmall',
			'required' 	 => true
			));		
		
		$this->addElement('submit', 'submit', array (
			'label' => 'Send',
			'class' => 'send'
			));
	}
}