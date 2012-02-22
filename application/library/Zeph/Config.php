<?php
/**
 * Created by JetBrains PhpStorm.
 * User: thomas
 * Date: 22/02/12
 * Time: 11:36 AM
 * To change this template use File | Settings | File Templates.
 */
class Zeph_Config extends Zend_Config_Ini
{
	protected static $_instance = null;
	var $_configPath;
	var $_paths;

	public function __construct($configPath = '', $section = null)
	{
		if ($configPath == '')
		{
			$configPath = $this->findConfigPath ();
		}

		$this->_configPath = $configPath;

		parent::__construct($this->_configPath, $this->getConfigName());

		// setup our app paths;
		$this->_paths=array();
		$this->_paths['PATH_ROOT'] = $this->_getPath(PATH_ROOT);
		$this->_paths['PATH_CORE'] = $this->_getPath(PATH_CORE);
		$this->_paths['PATH_LIBRARY'] = $this->_getPath(PATH_LIBRARY);
		$this->_paths['PATH_APPLICATION'] = $this->_getPath($this->zephyr->pathApplication);
		$this->_paths['PATH_CORE_APPLICATION'] = $this->_getPath($this->zephyr->pathCoreApplication);
		$this->_paths['PATH_STORAGE'] = $this->_getPath($this->zephyr->pathStorage);
		$this->_paths['PATH_PUBLIC'] = $this->_getPath($this->zephyr->pathPublic);
	}

	/**
	 * Get singleton instance
	 *
	 * @return  Zeph_Core $instance
	 */
	public static function getInstance($configPath = '', $section = null)
	{
		if(self::$_instance === null) {
			self::$_instance = new self($configPath, $section);
		}
		return self::$_instance;
	}

	/**
	 * @return string
	 */
	public static function getConfigName ()
	{
		return 'host_'.$_SERVER['SERVER_NAME'];
	}

	/**
	 * @return string
	 */
	public static function getPath ($path)
	{
		return self::getInstance()->_getPath($path);
	}

	/**
	 * @return Zend_Config
	 */
	public function getConfigModifiable ()
	{
		return new Zend_Config_Ini($this->Path,
			null,
			array('skipExtends'=> true,
			'allowModifications' => true));
	}

	/**
	 * @return string
	 */
	public function getConfigPath ()
	{
		return $this->Path;
	}

	/**
	 * @return string
	 * @throws Exception
	 */

	function findConfigPath ()
	{
		$iniFile = 'application.ini';
		$pathsToTry=array();

		// relative to script filename
		$scriptPath = dirname($_SERVER['SCRIPT_FILENAME']);
		$pathsToTry[] = $scriptPath.'/application/config/'.$iniFile;
		$pathsToTry[] = $scriptPath.'/../application/config/'.$iniFile;

		// relative to current file (Zeph_Core.php)
		$thisPath = dirname(__FILE__);
		$pathsToTry[] = $thisPath.'/../../application/config/'.$iniFile;

		foreach($pathsToTry as $path)
		{
			if (file_exists($path))
				return $path;
		}
		throw new Exception('Unable to find config file');
	}

	/**
	 * @param $path
	 * @return mixed
	 */
	public function _getPath ($path)
	{
		foreach ($this->_paths as $key=>$value)
			$path = str_replace('%'.$key.'%',$value, $path);

		return $path;
	}
}