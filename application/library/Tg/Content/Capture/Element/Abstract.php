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


class Tg_Content_Capture_Element_Abstract {
	protected $_type;
	protected $_id;
	protected $_label;
	protected $_xmlNode;
	protected $_value = "blank";
	
	function __construct($XMLNode=null) {
		
		$this->_xmlNode = $XMLNode;
		
		$this->_type = (string)$XMLNode->attributes()->type;
		$this->_id = (string)$XMLNode->attributes()->id;
		$this->_label = isset($XMLNode->attributes()->label)?(string)$XMLNode->attributes()->label:ucfirst((string)$XMLNode->attributes()->id);
	}
	
	function __get ($name)
	{
		if ($name == 'label')
			return $this->_label;
		elseif ($name == 'id')
			return $this->_id;
		elseif ($name == 'type')
			return $this->_type;
	}
//
//	function __toString () {
//		return $this->value;
//	}
//
//	function toXML () {
//		return '<Field_Abstract name="'.$this->name.'">'.$this->value.'</Field_Abstract>'."\n";
//	}

	public function addToForm ($Form) 
	{
		$Form->addElement('text', $this->_id, array (
	    					'label'=>ucfirst($this->_label), 
	    					'value'=>$this->_value, 
	    					'class'=>'text'));
		//return $id;
	}
	
	function updateFromForm ($data, $prefix='') {
		$id = $prefix.'_'.$this->name;
		$this->value = $data[$id];
	}
	
	function defaultXML () {
		return '<Field_Abstract name="'.$this->name.'"></Field_Abstract>'."\n";
	}
}
?>