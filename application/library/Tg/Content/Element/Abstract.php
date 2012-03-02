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
 * Tg_Content_Element_Abstract 
 */

class Tg_Content_Element_Abstract {
	protected $_type;
	protected $_id;
	protected $_uid;
	protected $_label;
	protected $_xmlNode;
	protected $_value = "blank";
	
	function __construct(SimpleXMLElement $XMLNode=null) {
		
		$this->_xmlNode = $XMLNode;
		
		$this->_type = (string)$XMLNode->getName();
		$this->_id = (string)$XMLNode->attributes()->id;
		$this->_uid = (string)$XMLNode->attributes()->uid;
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
	
	function getXmlAttribute ($name)
	{
		return $this->_xmlNode->attributes()->$name;
	}
	
	public function hasValue ()
	{
		if (!empty($this->_value) && $this->_value != "blank")
			return true;
		else 
			return false;
	}

	public function toJson ()
	{
		return '{"type":"'.$this->_type.'","id":"'.$this->_id.'","uid":"'.$this->_uid.'","label":"'.$this->_label.'","value":"'.$this->_value.'"}';
	}
}
?>