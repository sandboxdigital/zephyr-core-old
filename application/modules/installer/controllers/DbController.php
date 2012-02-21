<?php

require 'AbstractController.php';

class DbController extends AbstractController
{
	public function init()
	{
		parent::init();

		$this->_sqlPath = APPLICATION_PATH.'/sql';
	}

    public function indexAction()
    {
		$dbConection = $this->testDbConnection();
        $this->view->dbConnecting = $dbConection->connecting;
		$this->view->dbConnectingMessage = $dbConection->message;

		$files = array ();

		$fileNames = array ();

		if ($handle = opendir($this->_sqlPath)) {
			while (false !== ($file = readdir($handle))) {
				if (strpos($file,'.') > 0 || strpos($file,'.') === false) {
					$fileNames[] = $file;
				}
			}

			closedir($handle);
		}

		asort($fileNames);

		foreach ($fileNames as $file)
		{
			$ofile = new stdClass();
			$ofile->name = $file;
			$ofile->date = filemtime($this->_sqlPath.'/'.$file);
			$files[] = $ofile;
		}


		$this->view->scripts = $files;
	}

	public function viewAction()
	{
		$script = $this->_getParam('script');
		$sqlFileToExecute = $this->_sqlPath .'/'.$script;
		$f = fopen($sqlFileToExecute,"r");
		$sqlFile = fread($f,filesize($sqlFileToExecute));
		$this->view->script = $script;
		$this->view->sql = $sqlFile;
		fclose($f);
	}

	public function executeAction ()
	{
		if ($this->_getParam('script'))
			$this->view->response = $this->_execute($this->_getParam('script'));
		$this->view->script = $this->_getParam('script');
	}

	private function _execute($script)
	{
		$sqlErrorCode = 0;
		$sqlCurrentStmt = '';

		$db = Zend_Db::factory($this->_activeConfig->resources->db);
		$pdo = $db->getConnection();



		$sqlFileToExecute = $this->_sqlPath .'/'.$script;

		$f = fopen($sqlFileToExecute,"r");
		$sqlFile = fread($f,filesize($sqlFileToExecute));

		// ignores ; within strings from http://www.dev-explorer.com/articles/multiple-mysql-queries
		// doesn't work!
		//$sqlArray = preg_split("/;+(?=([^'|^\\\']*['|\\\'][^'|^\\\']*['|\\\'])*[^'|^\\\']*[^'|^\\\']$)/", $sqlFile);

		$pdo = $db->getConnection();

		$line = 0;
		$response = '';
		try {
			// works if we're using PDO
			$result = $pdo->prepare($sqlFile);
			if ($result->execute()) {
				$response .= 'Success<br/><br/>Error codes:<br/>';
				do {
					$response .=  "".$result->errorCode().' <br/>';
					++$line;
				}
				while($result->nextRowset()); // doesn't seem to return all rowsets!
			}
			$result = null;
		} catch (Exception $e)
		{
			$sqlErrorCode = $e->getCode();
			$sqlErrorText = $e->getMessage();
		}
		$response .= "<br/>Statements executed: $line<br/>";
		if ($sqlErrorCode == 0){
			$response .= "SQL script was finished succesfully!";
		} else {
			$response = "An error occured during SQL script execution!<br/>";
			$response .= "Error code: $sqlErrorCode<br/>";
			$response .= "Error text: $sqlErrorText<br/>";
			$response .= "Statement: <pre>$sqlCurrentStmt</pre>";
		}
		return $response;
	}
}

