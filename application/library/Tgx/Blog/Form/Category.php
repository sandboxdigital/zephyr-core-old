<?php

class Tgx_Blog_Form_Category extends Tg_Form  
{ 
    public function __construct($options = null) 
    { 
    	parent::__construct ($options);
    	
		$this->setAction($options['action'])
		     ->setMethod('post')
		     ->setAttrib('id', 'categoryEdit');
		
		$this->addElement('hidden', 'id');
			
		$this->addElement('text', 'name', array ('label'=>'Name', 'required'=>true));
			
		$this->addElement('text', 'slug', array ('label'=>'Slug', 'required'=>true));
			
		$this->addElement('text', 'position', array ('label'=>'Position', 'required'=>true));
			
		$t = new Tgx_Blog_Categories();
		
		
		$options = $t->fetchPairs('id', 'name');
		
		$options = array ('0'=>'No parent')+$options ;
		$this->addElement('select', 'parentId', array (
			'label'=>'Parent',
			'multioptions'=>$options
			));
		
		$this->addElement('submit','submit');
    } 
} 
?>