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


class Tg_Content_Capture_Element_GroupOption extends Tg_Content_Capture_Element_Abstract 
{
	private $_elements;
	
	function __construct($xmlNode) {
		parent::__construct ($xmlNode);
		
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
	
	public function addToForm (Zend_Form $Form) 
	{
		$subForm = new Zend_Form_SubForm();
		$Form->addSubForm($subForm, 'option_'.$this->_id);
		
		$subForm->clearDecorators()
		->addDecorator('FormElements')
	 	->addDecorator('FieldSet');

		foreach ($this->_elements as $key => $order) {
			$element = $this->getElement($key);
			$element->addToForm ($subForm);
		}
	}
	
	function __toString () {
		return $this->value;
	}
	
	function toXML () {
		return '<group name="'.$this->name.'"><![CDATA['.$this->value.']]></group>'."\n";
	}
}
?>