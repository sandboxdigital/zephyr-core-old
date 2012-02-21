<?php

require 'AbstractController.php';

class IndexController extends AbstractController
{
    public function indexAction()
    {
		$activeConfig = $this->getActiveConfig();
	    $activeConfigName = $this->getActiveConfigName();
	    $dbConnection = $this->testDbConnection();

	    $checks = array(
		    'Config' => array(
			    array(
				    'label'=>'Config exists for this server',
				    'check'=>$activeConfig !== null,
				    'fixUrl'=>array('controller'=>'config','action'=>'edit','section'=>$activeConfigName)
			    ),
		    ),
		    'Database' => array(
			    array(
				    'label'=>'Can connect to database',
				    'check'=>$dbConnection->connecting,
				    'fixUrl'=>array('controller'=>'config','action'=>'edit','section'=>$activeConfigName)
			    ),
			    array(
				    'label'=>'Database has tables',
				    'check'=>$dbConnection->tables,
				    'fixUrl'=>array('controller'=>'db','action'=>'index')
			    ),
		    ),
		    'Folders' => array(
			    array(
				    'label'=>'Storage folder exists',
				    'check'=>$this->checkFolderExists(STORAGE_PATH),
				    'fixUrl'=>array('controller'=>'index','action'=>'create-folders')
			    ),
			    array(
				    'label'=>'Cache folder exists',
				    'check'=>$this->checkFolderExists(STORAGE_PATH.'/cache'),
				    'fixUrl'=>array('controller'=>'index','action'=>'create-folders')
		        ),
			    array(
				    'label'=>'Session folder exists',
				    'check'=>$this->checkFolderExists(STORAGE_PATH.'/sessions'),
				    'fixUrl'=>array('controller'=>'index','action'=>'create-folders')
		        ),
			    array(
				    'label'=>'Uploads folder exists',
				    'check'=>$this->checkFolderExists(STORAGE_PATH.'/uploads'),
				    'fixUrl'=>array('controller'=>'index','action'=>'create-folders')
		        ),
			    array(
				    'label'=>'Tmp folder exists',
				    'check'=>$this->checkFolderExists(STORAGE_PATH.'/tmp'),
				    'fixUrl'=>array('controller'=>'index','action'=>'create-folders')
		        ),
			    array(
				    'label'=>'Incoming folder exists',
				    'check'=>$this->checkFolderExists(STORAGE_PATH.'/incoming'),
				    'fixUrl'=>array('controller'=>'index','action'=>'create-folders')
		        ),
           )
	    );


	    $this->view->checks = $checks;

    }

	function createFoldersAction ()
	{
		echo STORAGE_PATH;
		mkdir(STORAGE_PATH);
		mkdir(STORAGE_PATH.'/cache');
		mkdir(STORAGE_PATH.'/sessions');
		mkdir(STORAGE_PATH.'/uploads');
		mkdir(STORAGE_PATH.'/tmp');
		mkdir(STORAGE_PATH.'/incoming');
		$this->_redirect(array('action'=>'index'));
	}

	function checkFolderExists ($folder)
	{
		return file_exists($folder);
	}


	function checkLoadingPages ()
	{
		Zend_Registry::getInstance()->set('siteconfig', $this->getActiveConfig()->toArray());
		Zend_Registry::getInstance()->set('config', $this->getActiveConfig()->toArray());

	    Zend_Db_Table::setDefaultAdapter($this->getDatabase());

	    $rootPage = Tg_Site::getInstance()->getRootPage();
	    echo $rootPage->name;
	}
}

