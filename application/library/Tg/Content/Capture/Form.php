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

class Tg_Content_Capture_Form 
{
	private $_formPath;
	private $_config;
	private $_elements;
	private $_id = 'contentCaptureForm';
	
	public function __construct()
	{
		
	}
	
	
	public function __toString()
	{
		return $this->render();
	}
	
	public function load ($xmlFile)
	{
		$this->_config = Zend_Registry::getInstance()->get('siteconfig')->content->toArray();
		$this->_formPath = realpath($this->_config['capture']['formPath']);
		
		$xmlNode = simplexml_load_file($this->_formPath.'/'.$xmlFile);
		$this->parseXml ($xmlNode);
	}
		
	public function parseXml ($xmlNode)
	{
		$this->_elements = array ();
		
		if ($xmlNode->children ()) {
			foreach ($xmlNode->children () as $xmlField) {
				$id = (string)$xmlField->attributes()->id;
				$type = (string)$xmlField->getName();				
				
				$this->addElement ($type, $id, $xmlField);
			}
		}
	}
	
	public function addElement ($type, $id, $options)
	{
		/*switch ($type) {
		case 'html':
			$this->_elements[$name] = new Tg_Content_Capture_Element_Html ($options);
			break;
		case 'text':
		case 'string':
		case 'link':
			$this->_elements[$name] = new Tg_Content_Capture_Element_String ($options);
			break;
		case 'image':
		case 'file':
			$this->_elements[$name] = new Tg_Content_Capture_Element_File ($options);
		case 'group':
			$this->_elements[$name] = new Tg_Content_Capture_Element_Group ($options);
			break;
		default:
			throw new Zend_Exception('Unknown Element type: '.$type);
		}*/
		
		$elementClassName = "Tg_Content_Capture_Element_".ucfirst($type);
		$element = new $elementClassName ($options);
		
		$this->_elements[$id] = $element;
	}
	
	public function getElement ($id)
	{
		if (array_key_exists($id, $this->_elements)) {
            return $this->_elements[$id];
        }
        return null;
	}
	
	public function render ()
	{
		$Form = new Tg_Form ();
		$Form->addElement('hidden','elementId',array('value'=>$this->_id));
		
		//$this->addToForm($Form, false);
//		
//		$subForm = new Zend_Form_SubForm();
//		
//		$subForm->addElement('text','name',array ('label'=>'Name'));
//		
//		$subSubForm = new Zend_Form_SubForm();
//		
//		$subSubForm->addElement('text','name',array ('label'=>'Name'));
//		
//		$subForm->addSubForm($subSubForm, 'subform');
//		
//		$Form->addSubForm($subForm, 'subform');
		
		foreach ($this->_elements as $key => $order) {
			$element = $this->getElement($key);
			$element->addToForm ($Form);
		}
		
        $Form->addElement('submit', 'save', array ('label'=>'Save', 'class'=>'save'));
	     		
        $Form->addElement('button', 'cancel', array ('label'=>'Cancel', 'class'=>'cancel'));
        
		$Form->setElementDecorators(array('ViewHelper'), array('elementId','save','cancel'));
        
        
		return $Form->render ();
	}
}