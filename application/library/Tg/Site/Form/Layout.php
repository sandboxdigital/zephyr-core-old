<?php
/**
 * Tg Framework 
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://framework.zend.com/license/new-bsd
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@zend.com so we can send you a copy immediately.
 *
 * @copyright  Copyright (c) 2009 Thomas Garrood (http://www.garrood.com)
 * @license    New BSD License
 */

/**
 * Tg Site
 */

class Tg_Site_Form_Layout extends Tg_Form  
{ 
    public function __construct($data = null, $options = null) 
    { 
    	parent::__construct ($options);
    	
    	$this->setAction('/admin/site/layout-edit')
		     ->setMethod('post');
		
    	// hidden field
		$this->addElement('hidden', 'id', array(
			'decorators'=>'ViewHelper'
			));    	
		
		$this->addElement('text', 'name', array(
			'label'=>'Name',
			'required'=>true,
			'class'=>'text'
			));
			
		$this->addElement('text', 'layout_file', array(
			'label'=>'Layout file',
			'required'=>true,
			'class'=>'text'
			));
			
 		$this->addElement('submit','submit', array (
 			'class'=>'submit', 
 			'label'=>'Update'
 			));
    } 
} 
?>