<?php
class Tg_Db_ActiveRecord_Factory
{	
	public static $tables = array ();
	
	public static function getActiveRecordTable  ($name, $rowClassName)
	{
		if (!isset(self::$tables[$name]))
		{
			self::$tables[$name] = new Tg_Db_Table_Crud(array(
				'name'=>$name,
				'rowClass'=>$rowClassName
				));
		}
		return self::$tables[$name];
	}
}