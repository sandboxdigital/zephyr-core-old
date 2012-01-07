<?php

class Tgx_Blog_Form_Blog extends Tg_Form  
{ 
    public function __construct($options = null) 
    { 
    	parent::__construct ($options);
    	
		$this->setAction($options['action'])
		     ->setMethod('post')
		     ->setAttrib('id', 'blogEdit');
		
		$this->addElement('hidden', 'id');
			
		$this->addElement('text', 'name', array ('label'=>'Name', 'required'=>true));
			
		$this->addElement('text', 'slug', array ('label'=>'Slug', 'required'=>true));
		
		$this->addElement('submit','submit');
    } 
} 
?>