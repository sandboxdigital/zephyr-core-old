<?php
/**
 * Tg Site Page Form Class 
 *
 * @copyright  Copyright (c) 2009 Thomas Garrood (http://www.garrood.com)
 *
 */
class Tg_Site_Form_PageMeta extends Tg_Form  
{ 
    public function __construct($options = null) 
    { 
    	parent::__construct ($options);
    	
    	$this->setAttrib('id', 'formPmAdd');
		
    	// hidden fields
		$this->addElement('hidden', 'id', array ('decorators'=>array('ViewHelper')));
			
		$this->addElement('text', 'metaTitle', array(
			'label'		=> 'Title',
			'required'	=> true,
			'class'		=> 'text'
			));
			
		$this->addElement('textarea', 'metaKeywords', array(
			'label'		=> 'Keywords',
			'required'	=> true,
			'class'		=> 'text'
			));
			
		$this->addElement('textarea', 'metaDescription', array(
			'label'		=> 'Description',
			'required'	=> true,
			'class'		=> 'text'
			));
    } 
} 
?>