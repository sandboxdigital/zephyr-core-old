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
 * Tg_Content_Element_File 
 */

class Tg_Content_Element_File extends Tg_Content_Element_Abstract 
{	
	function __construct ($xmlNode) 
	{
		parent::__construct ($xmlNode);
		
		$this->_value = (string)$xmlNode;
	}
	
	function __toString()
	{		
		$file = Tg_File::getFile ($this->_value);
		
		if ($file)
			return $file->getUrl();
		else
			return 'No file';
	}

	public function toJson ()
	{
		$f = $this->getFile();
		$url = $f!=null?$f->getUrl():'';
		return '{"type":"'.$this->_type.'","id":"'.$this->_id.'","uid":"'.$this->_uid.'","label":"'.$this->_label.'","value":"'.$this->_value.'","url":"'.$url.'"}';
	}
	
	function getFile () 
	{
		return Tg_File::getFile ($this->_value);
	}
}

?>