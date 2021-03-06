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
 * Tg_Content_Element_Custom
 * 
 *  Custom admin element - doesn't do anything on frontend
 *  
 */

class Tg_Content_Element_Custom extends Tg_Content_Element_Abstract 
{		
	function __construct(SimpleXMLElement $xmlNode) 
	{
		parent::__construct ($xmlNode);
		
		$this->_value = (string)$xmlNode;
	}
	
	function __toString () 
	{
		return $this->_value;
	}
	
	function toString () 
	{
		return $this->__toString();
	}
	
	function fromPost ()
	{
		
	}
}
?>