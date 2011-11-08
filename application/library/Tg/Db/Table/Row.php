<?php
class Tg_Db_Table_Row extends Zend_Db_Table_Row_Abstract
{
	public function init()
    {
        // table schema
        if (($table = $this->_getTable())) {
            $info = $table->info();
            
            // automatically convert date fields to Zend_Date
            //TODO automatically unserialise arrays.... 
            foreach ($info['metadata'] as $name => $fieldMetadata) {            	
				if ($fieldMetadata['DATA_TYPE']=='timestamp' || $fieldMetadata['DATA_TYPE']=='datetime') {
					if (isset($this->_data[$name]))
					{
						if ($this->_data[$name]=='0000-00-00 00:00:00' || $this->_data[$name]==null)
							$this->_data[$name] = null;
						else {
							$this->_data[$name] = new Zend_Date ($this->_data[$name], Zend_Date::ISO_8601);
						}
					}
				} elseif ($fieldMetadata['DATA_TYPE']=='date') {
					if (isset($this->_data[$name]))
					{
						if ($this->_data[$name]=='0000-00-00' || $this->_data[$name]==null)
							$this->_data[$name] = null;
						else {
							$this->_data[$name] = new Zend_Date ($this->_data[$name], Zend_Date::ISO_8601);
						}
					}
				} 
			}
        }
    }
    
    // TODO - fix - doesn't get run by Zend_Json::encode() for some reason
    public function toJson ()
    {
    	$return = array ();
    	
        if (($table = $this->_getTable())) {
            $info = $table->info();
            
            foreach ($info['metadata'] as $name => $fieldMetadata) {
				$return[$name] = $this->_data[$name];
            	
//				if ($fieldMetadata['DATA_TYPE']=='timestamp' || $fieldMetadata['DATA_TYPE']=='datetime') {
//					if (isset($this->_data[$name]))
//					{
//						if ($this->_data[$name]=='0000-00-00 00:00:00' || $this->_data[$name]==null)
//							$this->_data[$name] = null;
//						else {
//							$this->_data[$name] = new Zend_Date ($this->_data[$name], Zend_Date::ISO_8601);
//						}
//					}
//				} elseif ($fieldMetadata['DATA_TYPE']=='date') {
//					if (isset($this->_data[$name]))
//					{
//						if ($this->_data[$name]=='0000-00-00' || $this->_data[$name]==null)
//							$this->_data[$name] = null;
//						else {
//							$this->_data[$name] = new Zend_Date ($this->_data[$name], Zend_Date::ISO_8601);
//						}
//					}
//				} 
			}
        }
        
        return $return;
    }
}
