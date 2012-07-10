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
 * Tg_Content_Element_Group 
 */

class Tg_Content_Element_Group extends Tg_Content_Element_Abstract implements SeekableIterator, Countable, ArrayAccess
{
	private $_options;
	private $_pointer;
	
	function __construct($xmlNode=null) {
		parent::__construct ($xmlNode);
		
		$this->_options = array ();
		$this->_pointer = 0;
		
		if ($xmlNode->children ()) {
			foreach ($xmlNode->children () as $xmlField) {
				$id = (string)$xmlField->attributes()->id;		
				
				$this->_options[] = new Tg_Content_Element_GroupOption ($xmlField);
			}
		}
	}
	
	function __toString()
	{
		return 'Tg_Content_Element_Group does not return a string use group[0]';
	}

    function toArray ()
    {
        return $this->_options;
    }

	public function toJson ()
	{
		$jsonElements = array ();
		foreach ($this->_options as $option)
		{
			$j = $option->toJson ();
			array_push($jsonElements,$j);
		}

		return '{"type":"'.$this->_type.'","id":"'.$this->_id.'","uid":"'.$this->_uid.'","label":"'.$this->_label.'","options":['.implode(',',$jsonElements).']}';
	}

    public function rewind()
    {
        $this->_pointer = 0;
        return $this;
    }

    public function current()
    {
        if ($this->valid() === false) {
            return null;
        }

        return $this->_options[$this->_pointer];
    }

    public function key()
    {
        return $this->_pointer;
    }

    public function next()
    {
        ++$this->_pointer;
    }

    public function valid()
    {
        return $this->_pointer < count($this->_options);
    }

    public function count()
    {
        return count($this->_options);
    }

    public function seek($position)
    {
        $position = (int) $position;
        if ($position < 0 || $position >= $this->_count) {
            throw new Zend_Exception("Illegal index $position");
        }
        $this->_pointer = $position;
        return $this;
    }

    public function offsetExists($offset)
    {
        return isset($this->_options[(int) $offset]);
    }

    public function offsetGet($offset)
    {
        $this->_pointer = (int) $offset;

        return $this->current();
    }

    /**
     * Does nothing
     * Required by the ArrayAccess implementation
     *
     * @param string $offset
     * @param mixed $value
     */
    public function offsetSet($offset, $value)
    {
    }

    /**
     * Does nothing
     * Required by the ArrayAccess implementation
     *
     * @param string $offset
     */
    public function offsetUnset($offset)
    {
    }
}
?>