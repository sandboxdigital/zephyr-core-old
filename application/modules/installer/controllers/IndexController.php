<?php

require 'AbstractController.php';

class IndexController extends AbstractController
{
    public function indexAction()
    {
		$activeConfig = $this->getActiveConfig();
	    $dbConnection = $this->testDbConnection();

		$this->view->hasActiveConfig = $activeConfig !== null;
	    $this->view->activeConfigName = $this->getActiveConfigName();
	    $this->view->hasDbConnection = $dbConnection->connecting;
	    $this->view->hasDbTables = $dbConnection->tables;
    }
}

