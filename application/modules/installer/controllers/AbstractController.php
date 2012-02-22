<?php

class AbstractController extends Zend_Controller_Action
{
	var $_config;
	var $_activeConfig;

	public function getConfig ()
	{
		return Zeph_Core::getInstance()->getConfigModifiable();
	}

	public function getActiveConfigName ()
	{
		return 'host_'.$_SERVER['SERVER_NAME'];
	}

	public function getActiveConfig ()
	{
		return Zeph_Core::getInstance()->getConfig();
	}

    public function getDatabase()
    {
	    return Zend_Db::factory($this->getActiveConfig()->resources->db);
    }

    public function testDbConnection()
    {
	    $return = new stdClass();
	    $return->connecting = false;
	    $return->tables = false;

	    if ($this->getActiveConfig()) {
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

	public function init()
	{
		parent::init();

		$this->view->activeConfigName = $this->getActiveConfigName();
		$this->view->activeConfigExists = $this->getActiveConfig() != null;
	}
}

