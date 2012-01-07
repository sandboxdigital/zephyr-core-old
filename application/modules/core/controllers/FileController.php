<?php

class FileController extends Tg_Site_Controller
{
	static $_visible = false;
	
	public function init () {
		$this->noLogPage = true;

		parent::init();
	}
	
	
	function deleteAction ()
	{
		$this->_helper->layout->disableLayout();
		$this->_helper->viewRenderer->setNoRender();
		
	    $params = $this->getRequest()->getParams();
		$query = $this->getRequest()->getQuery();
		
		
		try {
			$fullname = $params['name'];
				
			$fullPath = Tg_File::getCacheFolder().'/'.$fullname;
			
			if (!file_exists($fullPath))
				throw new Exception ('File not found: '.$fullPath);
			else 
				unlink($fullPath);
		}
		catch (Exception $e)
		{
			echo $e->getMessage();
		}
	}
	
	function downloadAction ()
	{
		$this->_helper->layout->disableLayout();
		$this->_helper->viewRenderer->setNoRender();
		
	    $params = $this->getRequest()->getParams();
		$query = $this->getRequest()->getQuery();
		
		
		try {
			$fullname = $params['name'];
			$file = Tg_File::getFileByName ($fullname);
			
			if (!$file) 
				throw new Exception ('File not found: '.$fullname);
				
			$fullPath = $file->getPath();
			
			if (!file_exists($fullPath))
				throw new Exception ('File not found: '.$file->fullname);
			
		    $fsize = filesize($fullPath);
		    $iFSize = sprintf("%u", filesize($fullPath));
		    
		    // if the file is large (roughly 50Mb) just redirect as it's probably too big to output 
		    
			//echo $iFSize;die;

			if ($iFSize > 50000000) 
		    {
		    	
			//echo $iFSize;die;
			$this->_redirect($file->getUrl());
		    	return;
		    }
		    	
		    $path_parts = pathinfo($fullPath);
				
			if ($fd = fopen ($fullPath, "r")) {
			    $ext = strtolower($path_parts["extension"]);
//			    switch ($ext) {
//			        case "pdf":
//			        header("Content-type: application/pdf"); // add here more headers for diff. extensions
//			        header("Content-Disposition: attachment; filename=\"".$path_parts["basename"]."\""); // use 'attachment' to force a download
//			        break;
//			        default;
			        header("Content-type: application/octet-stream");
			        header("Content-Disposition: attachment; filename=\"".$file->name."\"");
//			    }
			    header("Content-length: $fsize");
			    //header("Cache-control: private"); //use this to open files directly
			    $fp = fopen($file, "r"); 
			    ob_end_flush();
				while (!feof($fp))
				{
				    echo fread($fp, 65536); 
				    ob_flush(); 
				    flush(); // this is essential for large downloads
				}  
			}
			fclose ($fd);
		} 
		catch (Exception $e)
		{
			echo $e->getMessage();
		}	
	}

	public function uploadAction () {
		$this->_helper->layout->disableLayout();

		$return = Tg_File::createFromUpload('Filedata');
		if ($return instanceof Tg_File_Db_File)
			$this->view->file = Zend_Json::encode($return->toObject());
		else {
			$this->view->file = $return;
		}
	}

	public function uploadValumsAction () {
		$this->_helper->layout->disableLayout();

		$result = Tg_File::createFromValumsUpload('');
    	
    	if ($result instanceof Tg_File_Db_File)
    	{
			$file = $result;
//    		if ($folder = Tg_Documents::getFolderById($this->_getParam('folderId',0)))
//    		{
//    			$folder->addFile($result);
//    		}
    		
    		$result = array (
    			'success'=>'true'
    			,'file'=>$file->toObject()
    		);
    	}
    	
		echo htmlspecialchars(json_encode($result), ENT_NOQUOTES);
		die;
	}
	
	public function uploadTestAction ()
	{
		
		$this->_helper->layout->disableLayout();
		
		$form = new Tg_Form ();
		$form->setAction('/file/upload');
		
		$form->addElement ('file','Filedata', array(	
			'label'=>'File'
			));
			
			
		$form->addElement ('submit','submit', array(	
			'label'=>'Submit',
			));
			
		if ($this->_request->isPost()) {
			$form->isValid($this->_request->getPost());
		}
			
		$this->view->form = $form;
	}

	public function __call($method, $args) {

	    $params = $this->getRequest()->getParams();
		$query = $this->getRequest()->getQuery();

		$fullname = $params['action'];
		$file = Tg_File::getFileByName ($fullname);
		
		if ($file) {
			$this->_helper->layout->disableLayout();
			$this->_helper->viewRenderer->setNoRender();
			$file->display(Tg_File_Utils::getExtension($fullname), Tg_File_Utils::getImageSize($fullname));
		} else
			$this->_forward('error');
	}
}
?>
