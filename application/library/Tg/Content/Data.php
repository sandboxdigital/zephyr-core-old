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
 * Tg_Content_Data 
 */


class Tg_Content_Placeholder 
{
	var $_name;
	
	public function __construct($name) 
	{
		$this->_name = $name;
	}
	
	public function __get($name)
	{
		return new Tg_Content_Placeholder ($this->_name.'->'.$name);
	}
	
	public function __toString()
	{
    		$user = Tg_Auth::getAuthenticatedUser();
    		if ($user && $user->hasRole('SUPERUSER'))
    			return 'Content block doesn\'t exist: '.$this->_name;
    		else
    			return '';
	}
	
	public function getFile ()
	{
		return null;
	}
	
	public function hasContent ()
	{
		return false;
	}
}


class Tg_Content_Data 
{
	private $_elements;
		
	public function __construct($xml) 
	{
		$this->_elements = array ();
		
		if ($xml instanceof SimpleXMLElement) {
			if ($xml->children ()) {
				foreach ($xml->children () as $xmlChild) {
					$type = (string)$xmlChild->getName();
					$id = (string)$xmlChild->attributes()->id;
					$this->addElement($type, $id, $xmlChild);				
				}
			}
		}
	}
	
	public function __toString()
	{
		return '';
	}
	
	public function __get($name)
	{
		if (isset($this->_elements[$name]))
			return $this->_elements[$name];
		else
			return new Tg_Content_Placeholder ($name); // return a Placeholder so site doesn't fall over if content element doesn't exist
	}
	
	public function getElement ($path)
	{
		$paths = explode('/', $path);
		
		$current = $this;
		foreach ($paths as $path)
		{
			$current = $current->__get($path);			
		}
		return $current;
	}
	
	public function hasElement ($name)
	{
		return isset($this->_elements[$name]);
	}
	
	public function hasValue ($name)
	{
		if (!isset($this->_elements[$name]))
			return false;
		else {
			return $this->_elements[$name]->hasValue ();;
			
		}
	}
	
	public function addElement ($type, $id, $xml)
	{	
		$elementClassName = "Tg_Content_Element_".ucfirst($type);
		$element = new $elementClassName ($xml);
		
		$this->_elements[$id] = $element;
	}	
	
	public function render ($contentPathId)
	{
		$lang = Tg_Site::language();
		
		if (!empty($lang))
			$contentPath = $lang.'/'.$contentPathId;
		else		
			$contentPath = $contentPathId;
		
		// TODO - rewrite to use a 'content stack' an array of content paths to try, 
		// loop over paths and test to see if they have content.
			
		$content = Tg_Site::getInstance()->getCurrentPage()->getContent();			
		$element = $content->getElement($contentPath);
		
		if ($element instanceof Tg_Content_Placeholder || $element instanceof Tg_Content_Element_Group && count($element)==0)
		{	
			// element is empty ... check template for default content
			$templateContent = Tg_Site::getInstance()->getCurrentPage()->getTemplate()->getContent();			
			$element = $templateContent->getElement($contentPath);
			
			if ($element instanceof Tg_Content_Placeholder || ($element instanceof Tg_Content_Element_Group && count($element)==0))
			{
				// template content empty ... default to english
				if (!empty($lang))
					$contentPath = 'en/'.$contentPathId;
					
				$element = $content->getElement($contentPath);
				if ($element instanceof Tg_Content_Placeholder || ($element instanceof Tg_Content_Element_Group && count($element)==0))
				{
					$element = $templateContent->getElement($contentPath);
					echo $this->view->contentPartial ($element);
				}
				else
					echo $this->view->contentPartial ($element);
			}
			else
				echo $this->view->contentPartial ($element);	
		} else 
			echo $this->view->contentPartial ($element);		
	}

	public function toJson ()
	{
		$jsonElements = array ();
		foreach ($this->_elements as $key=>$value)
		{
			$j = $this->_elements[$key]->toJson ();
			array_push($jsonElements,$key.':'.$j);
		}

		return '{'.implode(',',$jsonElements).'}';
	}
}