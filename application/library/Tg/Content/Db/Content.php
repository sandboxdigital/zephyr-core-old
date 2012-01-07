<?php
class Tg_Content_Db_Content extends Tg_Db_Table_Row
{
	var $_content;
	
	
	/**
	 * 
	 * Enter description here ...
	 * @return Tg_Content_Data $data
	 */
	public function content ()
	{
		if (!isset($this->_content))
		    $this->_content = Tg_Content::loadXml ($this->data);
		
	    return $this->_content;
	}
}