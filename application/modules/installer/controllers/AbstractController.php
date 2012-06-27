<?php

class AbstractController extends Zend_Controller_Action
{
	var $_config;
	var $_activeConfig;

	public function getConfig ()
	{
		return Zeph_Config::getConfig();
	}

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

	public function init()
	{
		parent::init();
	}
}

