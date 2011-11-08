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

class Tgx_Portfolio_Form_Work extends Tgx_Form 
{
    public function __construct($options = null) 
    { 
    	parent::__construct ($options);
		
		$this->addElement('hidden', 'id', array ('decorators'=>array('ViewHelper')));
		
		$this->addElement('text', 'title', array (
			'label' 	 => 'Title',
			'required' 	 => true,
			'filters' 	 => array('StringTrim'),
			'validators' => array(
				array('StringLength', false, array(3, 50))
			),
			'class'		 => 'text'
		));

		$this->addElement('select', 'published', array (
			'label' 		=> 'Published',
			'required' 		=> true,
			'default'		=> 'No',
			'multiOptions' 	=> array ('No' => 'No', 'Yes' => 'Yes')
		));
		
		$this->addElement('select', 'type', array (
			'label' 		=> 'Type',
			'required' 		=> true,
			'default'		=> 'project',
			'multiOptions' 	=> array ('project' => 'Project', 'client' => 'Client', 'culture' => 'Culture', 'inspiration' => 'Inspiration')
		));
		
		$clientsTable  = new Tgx_Portfolio_Db_Table_Works ();
		$clients = $clientsTable->fetchPairs ('id', 'title', 'type="client"', 'title');
		
		$clients = array('0'=>'None')+$clients;
		
		$this->addElement('select', 'clientId', array (
			'label' 		=> 'Client',
			'required' 		=> true,
			'multiOptions' 	=> $clients
		));
		
		$this->addElement('textarea', 'description', array (
			'label' 	 => 'Description',
			'required' 	 => true,
			'filters' 	 => array('StringTrim'),
			'validators' => array(
				array('StringLength', false, array(3, 50))
			),
			'class'		 => 'textarea'
		));
		
		$this->addElement('swfUpload', 'thumbnail', array (
			'label' 		=> 'Thumbnail',
			'required' 		=> true
		));
		
		$this->addElement('content','contentXml', array(
			'label'=>'',
			'form'=>'portfolio.xml'
			));

		$this->addElement('submit', 'savework', array (
			'label' => 'Save Work >>',
			'class' => 'submit'
		));
	}	
}