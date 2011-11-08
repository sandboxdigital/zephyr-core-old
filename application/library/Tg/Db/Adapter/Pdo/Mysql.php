<?php


class Tg_Db_Adapter_Pdo_Mysql extends Zend_Db_Adapter_Pdo_Mysql
{

	// convert Zend_Date to ISO_8601
	// convert array to serialiszed
	public function query($sql, $bind = array())
	{
		if (is_array($bind)) {
			foreach ($bind as $name => $value) {
				if ($value instanceof Zend_Date) {
					$bind[$name] = $value->get(Zend_Date::ISO_8601);
				} else if (is_array($value)) {
					$bind[$name] = serialize($value);					
				}
			}
		}

		$return = parent::query($sql, $bind);
		return $return;
	}
}
