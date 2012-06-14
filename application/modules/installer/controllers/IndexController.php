<?php

require 'AbstractController.php';

class IndexController extends AbstractController
{
    public function indexAction()
    {
		$config = $this->getConfig();

        $activeConfigFound =  $config->getSectionName()==Zeph_Config::getConfigName();

	    $activeConfigName = Zeph_Config::getConfigName();
	    $dbConnection = $this->testDbConnection();

	    $checks = array(
		    'Config' => array(
			    array(
				    'label'=>'Config exists for this server',
				    'check'=>$activeConfigFound,
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
				    'check'=>$this->checkFolderExists(Zeph_Core::getPath('%PATH_STORAGE%')),
				    'fixUrl'=>array('controller'=>'index','action'=>'create-folders')
			    ),
			    array(
				    'label'=>'Cache folder exists',
				    'check'=>$this->checkFolderExists(Zeph_Core::getPath('%PATH_STORAGE%/cache')),
				    'fixUrl'=>array('controller'=>'index','action'=>'create-folders')
		        ),
			    array(
				    'label'=>'Session folder exists',
				    'check'=>$this->checkFolderExists(Zeph_Core::getPath('%PATH_STORAGE%/sessions')),
				    'fixUrl'=>array('controller'=>'index','action'=>'create-folders')
		        ),
			    array(
				    'label'=>'Uploads folder exists',
				    'check'=>$this->checkFolderExists(Zeph_Core::getPath('%PATH_STORAGE%/uploads')),
				    'fixUrl'=>array('controller'=>'index','action'=>'create-folders')
		        ),
			    array(
				    'label'=>'Tmp folder exists',
				    'check'=>$this->checkFolderExists(Zeph_Core::getPath('%PATH_STORAGE%/tmp')),
				    'fixUrl'=>array('controller'=>'index','action'=>'create-folders')
		        ),
			    array(
				    'label'=>'Incoming folder exists',
				    'check'=>$this->checkFolderExists(Zeph_Core::getPath('%PATH_STORAGE%/incoming')),
				    'fixUrl'=>array('controller'=>'index','action'=>'create-folders')
		        ),
           )
	    );


	    $this->view->checks = $checks;

    }

	function createFoldersAction ()
	{
		if (!file_exists(Zeph_Core::getPath('%PATH_STORAGE%'))) mkdir(Zeph_Core::getPath('%PATH_STORAGE%'));
		if (!file_exists(Zeph_Core::getPath('%PATH_STORAGE%/cache'))) mkdir(Zeph_Core::getPath('%PATH_STORAGE%/cache'));
		if (!file_exists(Zeph_Core::getPath('%PATH_STORAGE%/sessions'))) mkdir(Zeph_Core::getPath('%PATH_STORAGE%/sessions'));
		if (!file_exists(Zeph_Core::getPath('%PATH_STORAGE%/uploads'))) mkdir(SZeph_Core::getPath('%PATH_STORAGE%/uploads'));
		if (!file_exists(Zeph_Core::getPath('%PATH_STORAGE%/tmp'))) mkdir(Zeph_Core::getPath('%PATH_STORAGE%/tmp'));
		if (!file_exists(Zeph_Core::getPath('%PATH_STORAGE%/incoming'))) mkdir(Zeph_Core::getPath('%PATH_STORAGE%/incoming'));
		$this->_redirect(array('action'=>'index'));
	}

	function checkFolderExists ($folder)
	{
		return file_exists($folder);
	}


	function checkLoadingPages ()
	{
		Zend_Registry::getInstance()->set('siteconfig', $this->getConfig()->toArray());
		Zend_Registry::getInstance()->set('config', $this->getConfig()->toArray());

	    Zend_Db_Table::setDefaultAdapter($this->getDatabase());

	    $rootPage = Tg_Site::getInstance()->getRootPage();
	    echo $rootPage->name;
	}
}

