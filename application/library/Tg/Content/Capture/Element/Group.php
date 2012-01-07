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


class Tg_Content_Capture_Element_Group extends Tg_Content_Capture_Element_Abstract 
{
	private $_options;
	
	function __construct($xmlNode) {
		parent::__construct ($xmlNode);
		
		$this->_options = array ();
		
		if ($xmlNode->children ()) {
			foreach ($xmlNode->children () as $xmlField) {
				$id = (string)$xmlField->attributes()->id;
				//$type = (string)$xmlField->getName();				
				
				$this->_options[$id] = new Tg_Content_Capture_Element_GroupOption ($xmlField);
			}
		}
	}

	function addToForm ($Form) {
		$subForm = new Zend_Form_SubForm();
		$Form->addSubForm($subForm, 'group_'.$this->_id);
		
		$subForm->clearDecorators();
	 	$subForm->addDecorator('FormElements')
	 	->addDecorator('FieldSet');
		
		$menu = array();
		foreach ($this->_options as $key => $order) {
			$menu[]= $this->_options[$key]->label;
		}
		
		$menuLabel = implode (',',$menu);
		
		$subForm->addElement('static', 'menu', array ('label'=>$menuLabel));
		reset ($this->_options);
		foreach ($this->_options as $key => $order) {
//			echo $this->_options[$key]->label;
			$option = $this->getOption($key);
			try {
				$option->addToForm ($subForm);
			}
			catch (Exception $e)
			{
				echo $e;
			}
		}
	}
	
	public function getOption ($id)
	{
		return $this->_options[$id];
	}
	
	function render () {
		return $this->value;
	}
	
	function toXML () {
		return '<group name="'.$this->name.'"><![CDATA['.$this->value.']]></group>'."\n";
	}
}
?>