<?php
class Tgx_Maps_Form_Map extends Tgx_Form 
{
	function __construct($options = array())
	{
		parent::__construct($options);
		
		$this->addElement('hidden', 'id', array ('decorators'=>array('ViewHelper')));
		
		$this->addElement('text', 'name', array (
			'label' 	 => 'Name',
			'required' 	 => true,
			'filters' 	 => array('StringTrim'),
			'validators' => array(
				array('StringLength', false, array(3, 50))
			),
			'class'		 => 'text'
		));
		
		$this->addElement('select', 'type', array (
			'label' 		=> 'Type',
			'required' 		=> true,
			'default'		=> 'project',
			'multiOptions' 	=> array (
				'someguys' => 'Someguys'
				,'bars' => 'Bar'
				,'restaurants' => 'Restaurant' 
				,'inspiration' => 'Inspiration'
				,'partners' => 'Partner'
				,'parking' => 'Parking'
				,'transport' => 'Transport'
		)
		));
		
    	$this->addElement('map','geolatlng', array ('label'=>'Location'));
		

		$this->addElement('submit', 'submit', array (
			'label' => 'Save',
			'class' => 'submit'
		));
	}
}
    	
		
		