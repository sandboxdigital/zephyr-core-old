<?php
class Tg_Config
{
	protected static $_config;
	public static function get ($path)
	{
		if (self::$_config == null) 
			self::$_config = Zend_Registry::get('config');

			
		$configPath = explode('.', $path);
		
		$config = self::$_config;
		foreach ($configPath as $path) {
			if (isset($config[$path]))
			{
				$config = $config[$path];
			} else 
				return null;
		}
		return $config;
	}
}