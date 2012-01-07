<?php

class Tg_Db_Table_Crud extends Tg_Db_Table 
{
//    protected $_rowClass = 'Tg_Db_Table_Row_Crud';
	
	public function insert ($data) 
	{
		// load meta data
    	$cols = $this->_getCols();
 		$zDate = new Zend_Date();
			
    	if(isset($this->_metadata['created_at']))
			$data['created_at'] = $zDate->toString("YYYY-MM-dd HH:mm:ss");
    	if(isset($this->_metadata['updated_at']))
			$data['updated_at'] = $zDate->toString("YYYY-MM-dd HH:mm:ss");
		
		return parent::insert($data);
	}
	
	public function update ($data, $where) 
	{
		// load meta data
    	$cols = $this->_getCols();
    
    	if(isset($this->_metadata['updated_at'])){
			$zDate = new Zend_Date();
			$data['updated_at'] = $zDate->toString("YYYY-MM-dd HH:mm:ss");
		}
		
		return parent::update($data, $where);
	}
}

?>