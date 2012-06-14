<?php
/**
 * Created by JetBrains PhpStorm.
 * User: thomas
 * Date: 22/02/12
 * Time: 8:56 AM
 * To change this template use File | Settings | File Templates.
 */

class Zeph_Core
{
	protected static $_instance = null;
	var $_config;
    var $_paths;

	public function __construct()
	{
		require_once 'Tg/Core.php';

		require_once 'Zend/Loader/Autoloader.php';
		$autoLoader = Zend_Loader_Autoloader::getInstance();
		$autoLoader->registerNamespace('Zeph');
		$autoLoader->registerNamespace('Tg');
		$autoLoader->registerNamespace('Tgx');
	}

	/**
	 * Get singleton instance
	 *
	 * @return  Zeph_Core $instance
	 */
	public static function getInstance()
	{
		if(self::$_instance === null) {
			self::$_instance = new self();
		}
		return self::$_instance;
	}


    /**
     * @param $path
     * @return string
     */
    public static function getPath ($path)
    {
        $inst = self::getInstance();
        $inst->initAppPaths();
        return $inst->_getPath($path);
    }

    /**
     * @param $path
     * @return string
     */
    public function _getPath ($path)
    {
        foreach ($this->_paths as $key=>$value)
            $path = str_replace('%'.$key.'%',$value, $path);

        return $path;
    }

    public function initAppPaths ($forceReload = false)
    {
        if ($this->_paths && $forceReload == false)
                return;

        // setup our app paths;
        $this->_paths=array();
        $this->_paths['PATH_ROOT'] = $this->_getPath(PATH_ROOT);
        $this->_paths['PATH_CORE'] = $this->_getPath(PATH_CORE);
        $this->_paths['PATH_LIBRARY'] = $this->_getPath(PATH_LIBRARY);

        $config = Zeph_Config::getConfig();

        if (isset($config->zephyr)){
            if (isset($config->zephyr->pathApplication)) $this->_paths['PATH_APPLICATION'] = $this->_getPath($config->zephyr->pathApplication);
            if (isset($config->zephyr->pathCoreApplication)) $this->_paths['PATH_CORE_APPLICATION'] = $this->_getPath($config->zephyr->pathCoreApplication);
            if (isset($config->zephyr->pathStorage)) $this->_paths['PATH_STORAGE'] = $this->_getPath($config->zephyr->pathStorage);
            if (isset($config->zephyr->pathPublic)) $this->_paths['PATH_PUBLIC'] = $this->_getPath($config->zephyr->pathPublic);
        }
    }

	/**
	 * @return Zend_Db_Adapter_Abstract
	 */
    public function getDatabase()
    {
	    return Zend_Db::factory($this->getConfig()->resources->db);
    }

    public function testDbConnection()
    {
	    $return = new stdClass();
	    $return->connecting = false;
	    $return->tables = false;

	    if ($this->getConfig()) {
		    try
		    {
				$db = $this->getDatabase();
				$pdo = $db->getConnection();

		        $return->connecting = true;

				$result = $pdo->prepare('DESCRIBE site_page');
				if ($r = $result->execute()) {

		            $return->tables = true;
				}

		    } catch (Exception $ex)
		    {
				$return->message = $ex->getMessage();
		    }
	    } else {
			$return->message = 'No connection details in config';
	    }

	    return $return;
	}

	public function run ()
	{
		$start = microtime();

        $config =  Zeph_Config::getConfig();

		try {
			// Create application, bootstrap, and run
			$application = new Zend_Application(
				    Zeph_Config::getConfigName(),
				    $config
				);

			$application
				->bootstrap()
				->run();
		} catch (Exception $e)
		{
			dump ($e->getMessage());
			dump ($e->getTraceAsString());
			dump ('POST');
			dump ($_POST);
			dump ('GET');
			dump ($_GET);
			if (isset($_SESSION))
			{
				dump ('SESSION');
				dump ($_SESSION);
			}
		}

		$end = microtime();
	}
}