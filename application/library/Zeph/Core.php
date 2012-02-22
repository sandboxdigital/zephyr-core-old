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

	public function __construct($configPath = '', $section = null)
	{
		require_once 'Tg/Core.php';

		require_once 'Zend/Loader/Autoloader.php';
		$autoLoader = Zend_Loader_Autoloader::getInstance();
		$autoLoader->registerNamespace('Zeph');
		$autoLoader->registerNamespace('Tg');
		$autoLoader->registerNamespace('Tgx');

		$config = $this->getConfig($configPath,$section);
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
	 * @return Zeph_Config
	 */
	public function getConfig ()
	{
		return Zeph_Config::getInstance();
	}

	/**
	 * @return string
	 */
	public function getConfigName ()
	{
		return Zeph_Config::getInstance()->getConfigName();
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
		try {
			// Create application, bootstrap, and run
			$application = new Zend_Application(
				    $this->getConfigName(),
				    $this->getConfig()
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