<?php
/**
 * Tg Site Page Form Class 
 *
 * @copyright  Copyright (c) 2009 Thomas Garrood (http://www.garrood.com)
 *
 */
class Tg_Site_Form_Page extends Tg_Form  
{ 
    public function __construct($options = null) 
    { 
    	parent::__construct ($options);
    	
    	$this->setAction('/admin/pm/page-add')
		     ->setMethod('post')
		     ->setAttrib('id', 'formPmAdd');
		
    	// hidden fields
		if (isset($options['isAdd'])) {
	    		$this->addElement('hidden', 'parentId', array ('decorators'=>array('ViewHelper')));
		} else		
			$this->addElement('hidden', 'id', array ('decorators'=>array('ViewHelper')));
			
		$this->addElement('text', 'title', array(
			'label'=>'Title',
			'required'=>true,
			'class'		 => 'text'
			));
    	
		$this->addElement('text', 'name', array(
			'label'=>'Name/Path',
			'class'	 => 'text'
			));
			
		$templates = new Tg_Site_Db_PageTemplates();
		$options = $templates->fetchPairs('id','name',null,'name');
			
    	$this->addElement('select', 'templateId', array(
			'label'=>'Template',
			'required'=>true,
			'multiOptions'=>$options
			));
    	
		$this->addElement('text', 'action', array(
			'label'=>'Action ',
			'class'	 => 'text'
			));

	    $options = array ('1'=>'Yes','0'=>'No');
		$this->addElement('select', 'visible', array(
			'label'=>'Visible',
			'required'=>true,
			'multiOptions'=>$options
			));
			
		$layouts = new Tg_Site_Db_Themes();
		$options = $layouts->fetchPairs('id','name',null,'name');
			
    	$this->addElement('select', 'layoutId', array(
			'label'=>'Theme',
			'required'=>true,
			'multiOptions'=>$options
			));
			
 		$this->addElement('submit','submit', array ('label'=>'Save', 'class'=>'submit'));
    } 
} 
?>