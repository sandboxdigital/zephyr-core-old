<?php
class Tg_Reports_Form extends Tg_Form
{
	function __construct($options = array ())
	{
		parent::__construct($options);
		
		$this->addElement('select','report', array(
			'label'=>'Report',
			'required' 	 => true,
			'class' 	 => 'select'		
			));
		
		$this->addElement('submit', 'go', array (
			'label' => 'Submit',
			'class' => 'submit'
			));
	}
}