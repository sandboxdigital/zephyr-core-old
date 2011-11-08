<?php

class Tg_Tree_Adapter_Db_Node
{
	protected $_data;
	protected $_parent;
	protected $_childNodes;
	protected $_row;
	
	public function __construct ($row, $parent)
	{
		$this->_row = $row;
		$this->_parent = $parent;
		$this->_childNodes = array ();
	}
	
	public function __get ($name)
	{
		if ($name == 'childNodes')
			return $this->getChildNodes ();
		else if ($name == 'firstChild') {
			$keys = array_keys($this->_childNodes);
			return $this->_childNodes[$keys[0]];
		} else 
		{
			return $this->_row->$name;			
		}		
	}
	
	public function update ($data)
	{
		$this->_row->setFromArray($data);
		$this->_row->save();
	}
	
	public function populateChildNode ($newNode)
	{
		$this->_childNodes[$newNode->id] = $newNode;
	}
	
	public function getChildNodes ()
	{
		return $this->_childNodes;
	}
	
	public function toArray ()
	{
		return $this->_data;
	}
}