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
 * Tg_Content_Element_GroupOption 
 */

class Tg_Content_Element_GroupOption extends Tg_Content_Element_Abstract 
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
	
	function __get($name)
	{
		if ($name == 'type')
			return $this->_type;
		elseif ($name == 'id')
			return $this->_id;
		elseif (isset($this->_elements[$name]))
			return $this->_elements[$name];
		else
			return parent::__get($name);
	}
	
	function __toString()
	{
		return 'Tg_Content_Element_GroupOption does not return a string. <br />Try accessing a child: '.implode (',',array_keys($this->_elements));
	}

	public function toJson ()
	{
		$jsonElements = array ();
		foreach ($this->_elements as $key=>$value)
		{
			$j = $this->_elements[$key]->toJson ();
			array_push($jsonElements,$key.':'.$j);
		}

		$jA = implode(',',$jsonElements);

		return '{"type":"'.$this->_type.'","id":"'.$this->_id.'","uid":"'.$this->_uid.'","label":"'.$this->_label.'","elements":{'.implode(',',$jsonElements).'}}';
	}
	
	public function addElement ($type, $id, $xml)
	{
		$elementClassName = "Tg_Content_Element_".ucfirst($type);
		$element = new $elementClassName ($xml);
		
		$this->_elements[$id] = $element;
	}
	
	public function getElement ($id)
	{
		if (array_key_exists($id, $this->_elements)) {
            return $this->_elements[$id];
        }
        return null;
	}
}
?>