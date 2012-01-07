<?php
/**
 * Tg Site Page Database Gateway Class 
 *
 * @copyright  Copyright (c) 2009 Thomas Garrood (http://www.garrood.com)
 *
 */
require_once 'Zend/Db/Table/Abstract.php';

class Tg_Site_Db_PageTemplates extends Tg_Db_Table 
{
	protected $_name = 'site_page_template';
	protected $_sequence = true;
	protected $_rowClass = 'Tg_Site_Db_PageTemplate';
	
	public function getByModuleAndController ($module, $controller)
	{
		$select = $this->select()
			->where('module=?',$module)
			->where('controller=?',$controller);
			
		$rows = $this->fetchAll($select);
		
		if ($rows->count()>0)
			return $rows[0];
		else
			return null;
	}
}
?>