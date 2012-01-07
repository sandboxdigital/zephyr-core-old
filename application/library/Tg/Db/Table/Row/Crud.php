<?php
class Tg_Db_Table_Row_Crud extends Zend_Db_Table_Row
{
	// don't need to use the class with Tg_Db_Table_Crud as that will set the created_at and updated_at fields as well
	
    protected function _insert()
    {
    	$metadata = $this->_table->info(Zend_Db_Table_Abstract::METADATA);
    	
    	if(isset($metadata['created_at']))
			$this->_data['created_at'] = new Zend_Date ();
    	if(isset($metadata['updated_at']))
			$this->_data['updated_at'] = new Zend_Date ();
    }

    protected function _update()
    {
    	$metadata = $this->_table->info(Zend_Db_Table_Abstract::METADATA);
    	
    	if(isset($metadata['updated_at']))
			$this->_data['updated_at'] = new Zend_Date ();
    }
}
