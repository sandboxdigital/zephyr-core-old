<?php
abstract class Tg_Db_Table extends Zend_Db_Table
{
    protected $_rowClass = 'Tg_Db_Table_Row';
    protected $_rowsetClass = 'Tg_Db_Table_Rowset';
	
	function count () {
		$select = $this->select()
			->from($this->_name, array('count' => 'COUNT(DISTINCT(id))'));
		$countResult = $this->fetchRow($select)->toArray();
		return $countResult['count'];	
	}
	
	function fetchPairs ($key, $value, $where=null, $order=null) {
		$select = $this->select()
			->setIntegrityCheck(true)
			->from($this->_name)
			->columns(array($key,$value));
		if ($where)
			$select->where($where);
		if ($order)
			$select->order($order);
		
		$rows = $this->fetchAll($select)->toArray();
		$pairs = array ();
		foreach ($rows as $row)
			$pairs[$row[$key]] = $row[$value];	
		
		return $pairs;
	}
	
	function fetchArray ($value, $where=null, $order=null) {
		if ($where instanceof Zend_Db_Select)
			$select = $where;
		else {
			$select = $this->select();
				
			if (($where))
				$select->where($where);
			if (($order))
				$select->order($order);
		}
		
		$select->setIntegrityCheck(true)
		->from($this->_name)
		->columns(array($value));
		
		$rows = $this->fetchAll($select)->toArray();
		
		$pairs = array ();
		foreach ($rows as $row)
			$pairs[] = $row[$value];	
		
		return $pairs;
	}
	
	
	public function exists ($data)
	{
		$select = $this->select ();
		foreach ($data as $key => $value) {
			$select->where($key.'=?',$value);
		}
		$rows = $this->fetchAll($select);
		if ($rows->count()==0)
			return false;
		else
			return $rows;
	}
	
	/**
	 * Fetches a row based on it's id
	 * 
	 * @param $id
	 * @return Model_Tour
	 */
	public function getById ($id) 
	{
		$select = $this->select()
		->where('id=?',$id);
    	return $this->fetchRow($select);
	}
	
	
	/**
	 * Fetches rows based on a set of ids
	 * 
	 * @param $id
	 * @return Model_Tour
	 */
	public function getByIds ($ids) 
	{
		$select = $this->select();		
		if (is_array($ids))
			$select->where('id IN (?)',implode(',', $ids));
		 else 
			$select->where('id IN (?)',$ids);
    	return $this->fetchAll($select);
	}
}
