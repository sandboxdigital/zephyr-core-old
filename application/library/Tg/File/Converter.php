<?php
class Tg_File_Converter
{
	protected $_enabled;
	protected $_config;
	protected $_log='';
	
	protected static $_instance = null;

	/**
	 * Get singleton instance
	 *
	 * @return  Tg_File_Converter $instance
	 */
	public static function getInstance() {
		if(self::$_instance === null) {
			self::$_instance = new self();
		}
		return self::$_instance;
	}
	
	public static function getOption ($name)
	{
		$inst = self::getInstance();
		if (isset($inst->_config[$name]))
			return $inst->_config[$name];
		else
			return false;
	}
	
	public static function log ($log)
	{
		$inst = self::getInstance();
		$inst->_log .= $log."\n";
	}
	
	public static function getLog ()
	{
		$inst = self::getInstance();
		return $inst->_log;
	}
	
	public static function clearLog ()
	{
		$inst = self::getInstance();
		$inst->_log = '';
	}	
	
	/**
	 * Converts a file to a web viewable format (if possible)
	 * 
	 * @throws Zend_Exception
	 */
	
	public static $FORCE_NONE = 0;
	public static $FORCE_ALL = 1;
	public static $FORCE_FAIL = 2;
	public static $FORCE_NOTFOUND = 3;
	
	public static function convert ($file, $forceConversion=0)
	{
		$inst = self::getInstance();
		
		if (!$inst->_enabled)
			return;
		
		if (!($file instanceof Tg_File_Db_File))
			return;
			
		self::log ('');
		self::log ('Converting '.$file->name);
		
		if (($file->converted == '')
		|| ($file->converted == 'FAIL'  && $forceConversion==self::$FORCE_FAIL)
		|| ($file->converted == 'NOTFOUND'  && $forceConversion==self::$FORCE_NOTFOUND)
		|| ($forceConversion==self::$FORCE_ALL))
		{
			// continue
		}
		else
		{
			self::log ("\nConversion already attempted - status=".$file->converted.', force='.$forceConversion);
			return;
		}
			
		$plugins = self::getOption('plugin');
		// loop over converter plugins and try to convert file
		foreach ($plugins as $plugin)
		{
			self::log ("\nTrying plugin ".$plugin);
			$converter = new $plugin ();
			if ($converter->canConvert($file))
			{
				$file->converted = 'CONVERTING';
				$file->save ();
				if ($result = $converter->convert ($file, $forceConversion))
				{
					self::log ("Success with plugin ".$plugin);
					$file->converted = 'SUCCEED';
					$file->save ();
					return true;
				} else {
					self::log ("Fail with plugin ".$plugin);
					$file->converted = 'FAIL';
					$file->save ();
					return false;
				}
			}
		}
		self::log ("\nNo more plugins ");
		
		$file->converted = 'NOTFOUND';
		$file->save ();
		
    	return false; 
	}

	public function __construct() 
	{
		$this->_enabled = false;
		$this->_config  = array();
		$config = Zend_Registry::get('config');
		
		if (isset($config['file']))
		{
			if (isset($config['file']['converter']))
			{
				$this->_config  = $config['file']['converter'];
				if (isset($config['file']['converter']['enabled']))
					$this->_enabled = $config['file']['converter']['enabled'];	
			}
		}
	}	
	
}