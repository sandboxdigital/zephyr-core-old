<?php
/**
 * Tg Site Page Template Form Class 
 *
 * @copyright  Copyright (c) 2009 Thomas Garrood (http://www.garrood.com)
 *
 */

class Tg_Site_Form_PageTemplate extends Tg_Form  
{ 
    public function __construct($data = null, $options = null) 
    { 
    	parent::__construct ($options);
    	
    	$this->setAction('/admin/pm/template-add')
		     ->setMethod('post')
		     ->setAttrib('id', 'formPmAdd');
		
    	// hidden field
		$this->addElement('hidden', 'id');    	
		
		$this->addElement('text', 'name', array(
			'label'=>'Name',
			'required'=>true,
			'class'=>'text'
			));
			
		$this->addElement('text', 'module', array(
			'label'=>'Module',
			'required'=>true,
			'class'=>'text'
			));
			
		$this->addElement('text', 'controller', array(
			'label'=>'Controller',
			'required'=>true,
			'class'=>'text'
			));
			
//		$this->addElement('text', 'action', array(
//			'label'=>'Action',
//			'class'=>'text'
//			));
			
		$this->addElement('text', 'form', array(
			'label'=>'Content XML File',
			'class'=>'text'
			));
			
		$this->addElement('text', 'defaultSubPageTemplate', array(
			'label'=>'Default Subpage Template',
			'class'=>'text'
			));
			
		$this->setElementDecorators(array('ViewHelper'), array('id'));
		
 		$this->addElement('submit','submit', array ('class'=>'submit', 'label'=>'Update'));
    } 
} 
?>