<?php

class Tg_Tree_Node
{
	protected $_data;
//	protected $_left;
//	protected $_right;
//	protected $_id;
	protected $_parent;
	protected $_childNodes;
	
	public function __construct ($data, $parent)
	{
		$this->_data = $data;
//		$this->_left = $data['left'];
//		$this->_right = $data['right'];
		$this->_parent = $parent;
		
		$this->_childNodes = array ();
	}
	
	public function __get ($name)
	{
		if (isset($this->_data[$name]))
			return $this->_data[$name];
		else if ($name == 'childNodes')
			return $this->getChildNodes ();
		else if ($name == 'firstChild') {
			$keys = array_keys($this->_childNodes);
			return $this->_childNodes[$keys[0]];
		}		
	}
	
	public function populateChildNode ($newNode)
	{
		$this->_childNodes[$newNode->id] = $newNode;
	}
	
	public function &getChildNodes ()
	{
		return $this->_childNodes;
	}
	
	public function toArray ()
	{
		return $this->_data;
	}
}