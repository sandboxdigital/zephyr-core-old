<?php

class Core_Admin_SystemController extends Tg_Site_Controller
{
	protected static $_visible = true;

	public function init()
	{
		parent::init();


		$this->_sqlPath = APPLICATION_PATH.'/sql';
	}

	public function indexAction()
	{
	}

	public function modulesAction()
	{
		$this->view->installedModules = Tg_System::getInstalledModules();
		$this->view->availableModules = Tg_System::getAvailableModules();
	}

	public function infoAction()
	{
		phpinfo();
	}

	public function databaseAction()
	{
		if ($this->_getParam('script'))
			$this->_execute($this->_getParam('script'));
		 
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

	public function databaseViewAction()
	{
		$script = $this->_getParam('script');
		$sqlFileToExecute = $this->_sqlPath .'/'.$script;	
		$f = fopen($sqlFileToExecute,"r");
		$sqlFile = fread($f,filesize($sqlFileToExecute));	
		$this->view->script = $script;
		$this->view->sql = $sqlFile;		
		fclose($f);
	}

	private function _execute($script)
	{
		$sqlErrorCode = 0;
		$sqlCurrentStmt = '';
		$db = Zend_Registry::get('db');
		$db->getProfiler()->setEnabled(true);

		$sqlFileToExecute = $this->_sqlPath .'/'.$script;

		$f = fopen($sqlFileToExecute,"r");
		$sqlFile = fread($f,filesize($sqlFileToExecute));

		// ignores ; within strings from http://www.dev-explorer.com/articles/multiple-mysql-queries
		// doesn't work!
		//$sqlArray = preg_split("/;+(?=([^'|^\\\']*['|\\\'][^'|^\\\']*['|\\\'])*[^'|^\\\']*[^'|^\\\']$)/", $sqlFile);

		$pdo = $db->getConnection();

		$line = 0;

		try {
			// works if we're using PDO
			$result = $pdo->prepare($sqlFile);
			if ($result->execute()) {
				echo 'Success<br/><br/>Error codes:<br/>';
				do {
					echo "".$result->errorCode().' <br/>';
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
		echo "<br/>Statements executed: $line<br/>";

		/*
			foreach ($sqlArray as $sql) {
			if (strlen($sql)>3){
			$sqlCurrentStmt = $sql;
			echo "Statement: <pre>$sqlCurrentStmt</pre>";
			$result = $db->query($sql);
			//					$stmt->execute();
			if (!$result){
			$sqlErrorCode = mysql_errno();
			$sqlErrorText = mysql_error();
			break;
			}
			}
			}
			} catch (Exception $e)
			{
			$sqlErrorCode = $e->getCode();
			$sqlErrorText = $e->getMessage();
			}*/
		if ($sqlErrorCode == 0){
			$response = "SQL script was finished succesfully!";
		} else {
			$response = "An error occured during SQL script execution!<br/>";
			$response .= "Error code: $sqlErrorCode<br/>";
			$response .= "Error text: $sqlErrorText<br/>";
			$response .= "Statement: <pre>$sqlCurrentStmt</pre>";
		}

		echo $response;

		die;
		$this->view->response = $response;
	}
}

?>