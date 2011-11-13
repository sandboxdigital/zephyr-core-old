<?php

class Core_Admin_IndexController extends Tg_Site_Controller
{
    public function indexAction() {
    }

	public function infoAction() {
		phpinfo();
		$this->_helper->layout->disableLayout();
		$this->_helper->viewRenderer->setNoRender();
	}
	
	public function fileBrowserAction ()
	{
		$type = $this->_getParam('type','file');
		if ($type=='file') {
			$fileTable = new Tg_File_Db_Table_File();
			$select = $fileTable->select()->order ('name');
			$this->view->files = $fileTable->fetchAll ($select);
		} else {
			$this->view->page = Tg_Site::getInstance()->getRootPage();
		}
		$this->_helper->layout->setLayout('ajax');	
	}
	
	
	/**
	 * Following actions require admin privs taken from various admin controllers, some of which required superuser privs
	 * TODO - probably a better place to put these - some kind of ToolsController or SharedController or something
	 */

	/**
	 * Used for saving/updating an existing page or creatign a new one
	 */
	// TODO - move code into Tg_Site, too much code in controller
    public function sitePageSaveAction ()
    {
    	$response = new stdClass();
	    $response->success = true;
	    
    	try {
    		$pageId = $this->_getParam ('id',0);
    		$parentId = $this->_getParam ('parentId',0);
    		
    		if ($pageId<=0 && $parentId<=0)
				throw new Exception ("Page updated failed - no page id or parentId");
    		elseif ($pageId>0) {
		    	$Page = Tg_Site::getInstance ()->getPageById($pageId);
				if (!$Page)
					throw new Exception ("Page updated failed - page not found");
					
				$Page->update ($_POST);
				
				$response->msg = "Page updated";
			} elseif ($parentId>0) {
				$Parent = Tg_Site::getInstance ()->getPageById($this->_getParam ('parentId'));
				if (!$Parent)
					throw new Exception ("Page updated failed - parent page not found");
				
				$Page = Tg_Site::getInstance ()->appendPage ($_POST, $Parent);
				
				$response->msg = "Page added";
			} else {
				$Page->update ($_POST);
				
				$response->msg = "Page updated";
			}
			$response->page=$Page->toStdObject('read',false);
		} catch (Zend_Exception $exp)
		{
			$response->success = false;
			$response->msg = $exp->getMessage ();	
		}
		
		echo Zend_Json::encode ($response);
		die;		
    }

    // TODO - check user has privs to move page
    public function sitePageMoveAction ()
    {
    	$Pm = Tg_Site::getInstance();    	
    	$Parent = $Pm->getPageById($this->_getParam("parentId"));
    	$Parent->movePage ($this->_getParam("pageId"), $this->_getParam("previousSiblingId",0));
		
		echo '{"success":true,"msg":"Move successful"}';
		die;
    }

    // TODO - check user has privs to delete page
    public function sitePageDeleteAction ()
    {
		$Page = Tg_Site::getInstance ()->getPageById($this->_getParam ('id'));
		if (!$Page)
			throw new Exception ("Page not found");
			
		if ($Page->locked)
			throw new Exception ("Page is locked");
		
    	$Page->delete();
    	
		echo '{"success":true,"msg":"Delete successful"}';
		die;
    }	
    
    
    /*******************************************************
     * File/folder ajax actions
     */

    function fileFolderAddAction ()
    {
    	$response = new stdClass();
	    $response->success = true;
	    $response->msg = "Folder added";
	    
		$requestData = $this->_request->getParams();
		
		$parentFolder = Tg_Documents::getFolderById ($this->_getParam('parentId')); 
		
		$folder = Tg_Documents::addFolder($parentFolder, $requestData);
		
		$response->folder = $folder->toObject();
				
		echo Zend_Json::encode ($response);
		die;		
    }
    
    function fileFolderEditAction ()
    {
    	$response = new stdClass();
	    $response->success = true;
	    $response->msg = "Folder updated";
	    
		$requestData = $this->_request->getParams();
    	$folder = Tg_Documents::getFolderById ($this->_getParam('id')); 
		$folder->update ($requestData);
		
		$response->folder = $folder->toObject();
				
		echo Zend_Json::encode ($response);
		die;		
    }

	
    public function fileFolderListAction()
    {
    	echo Zend_Json::encode (Tg_Documents::getRootFolder ());
		die;	
    }
	
	public function fileListAction ()
	{
		$folderId = $this->_getParam('folderId');
		$fileTable = new Tg_File_Db_Table_File();
		$select = $fileTable->select(true);
		
		if ($folderId > 0) {
			$select->setIntegrityCheck(false)
	//		->order ('created_at')
			->order ('file.id ASC')
			->joinLeft('file_folder_file', 'file.id=file_folder_file.file_id',"")
			->where ('file_folder_file.folder_id=?',$folderId)	;
		} else 
		{
			$select->setIntegrityCheck(false)
	//		->order ('created_at')
			->order ('file.id ASC')
			->joinLeft('file_folder_file', 'file.id=file_folder_file.file_id ',"")
			->where ('file_folder_file.folder_id IS NULL')	;
		}
		
		$this->view->files = $fileTable->fetchAll ($select);
		
		$this->_helper->layout->setLayout('ajax');
	}
	
	
    public function fileFolderDeleteAction()
    {
    	$response = new stdClass();
	    $response->success = true;
	    $response->msg = "Folder deleted";
	    
    	$folder = Tg_Documents::getFolderById ($this->_getParam('id'));  
		
    	Tg_Documents::deleteFolder($folder);
				
		echo Zend_Json::encode ($response);
		die;		
    }
    
    public function fileUploadValumsAction()
    {
    	$result = Tg_File::createFromValumsUpload('');
    	
    	if ($result instanceof  Tg_File_Db_File)
    	{
			$file = $result;
    		if ($folder = Tg_Documents::getFolderById($this->_getParam('folderId',0)))
    		{
    			$folder->addFile($result);
    		}
    		
    		$result = array (
    			'success'=>'true'
    			,'file'=>$file->toObject()
    		);
    		
    		//Tg_File_Converter::convert ($file);
    	}
    	
		echo htmlspecialchars(json_encode($result), ENT_NOQUOTES);
		die;
    } 
    
    public function fileFolderRemoveFileAction()
    {
		$file = Tg_File::getFileById($this->_getParam('fileId'));
		$folder = Tg_Documents::getFolderById($this->_getParam('folderId',0));
		$folder->removeFile($file);
		
		$result = array (
			'success'=>'true'
			);
		
		echo htmlspecialchars(json_encode($result), ENT_NOQUOTES);
		die;
	} 

	public function fileFolderAddFileAction()
	{
		$file = Tg_File::getFileById($this->_getParam('fileId'));
		$folder = Tg_Documents::getFolderById($this->_getParam('folderId',0));
		$folder->addFile($file);
		
		$result = array (
			'success'=>'true'
			);
		    
		echo htmlspecialchars(json_encode($result), ENT_NOQUOTES);
		die;
	} 
	
    public function debugAction() 
    {
    	Tg_Log::logToDb("debug: \n".$this->_getParam('info'));
    	
    	echo '{success:"true"}';
    	die;
    }  
}

?>