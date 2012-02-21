<?php

class AbstractController extends Zend_Controller_Action
{
	var $_config;
	var $_activeConfig;

	public function getConfig ()
	{
		if (!$this->_config)
		{
			$this->_config = new Zend_Config_Ini(APPLICATION_PATH . '/config/application.ini',
				null,
				array('skipExtends'=> true,
				'allowModifications' => true));
		}
		return $this->_config;
	}

	public function getActiveConfigName ()
	{
		return 'host_'.$_SERVER['SERVER_NAME'];
	}

	public function getActiveConfig ()
	{
		if (!$this->_activeConfig)
		{
		    try
		    {
				$this->_activeConfig = new Zend_Config_Ini(APPLICATION_PATH . '/config/application.ini',
					$this->getActiveConfigName());
		    } catch (Exception $ex)
		    {
		        $this->_activeConfig = null;
		    }
		}
		return $this->_activeConfig;
	}

    public function testDbConnection()
    {
	    $return = new stdClass();
	    $return->connecting = false;
	    $return->tables = false;

	    if ($this->getActiveConfig()) {
		    try
		    {
				$db = Zend_Db::factory($this->getActiveConfig()->resources->db);
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

	public function init()
	{
		parent::init();

		$this->view->activeConfigName = $this->getActiveConfigName();
	}
}

