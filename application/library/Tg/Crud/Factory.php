<?php
class Tg_Crud_Factory 
{
	protected $_table;

	public function getAll() 
	{
		$select = $this->_table->select();

		return $this->_table->fetchAll($select);
	}
	
	public function getItem ($id) 
	{
		return $this->_table->fetchRow ("id=".$id);
	}

	public function getNew ()
	{
		return $this->_table->fetchNew ();
	}
	
	public function deleteItem ($id) 
	{
		return $this->_table->delete ("id=".$id);
	}
}