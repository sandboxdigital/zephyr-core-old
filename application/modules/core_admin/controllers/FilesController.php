<?php
/**
 * Tg Framework 
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://framework.zend.com/license/new-bsd
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@zend.com so we can send you a copy immediately.
 *
 * @copyright  Copyright (c) 2009 Thomas Garrood (http://www.garrood.com)
 * @license    New BSD License
 */

class Core_Admin_FilesController extends Tg_Content_Controller
{
	public function init ()
	{
    	$this->_showPageBar = false;
    	
		parent::init();
	}
	
	public function browseAction ()
	{
		$type = $this->_getParam('type','file');
		if ($type=='file' || $type=='image') {
			$fileTable = new Tg_File_Db_Table_File();
			$select = $fileTable->select()->order ('id DESC');
			$files2 = $fileTable->fetchAll ($select);
			
			$files = array ();
			foreach ($files2 as $file)
			{
				$files[] = $file->toObject();
			}
			
			$this->view->files = Zend_Json::encode ($files);
		} else {
			$this->view->page = Tg_Site::getInstance()->getRootPage();
		}
		$this->_helper->layout->disableLayout();
	}
	
    public function indexAction()
    {
		
		$this->view->headLink()->appendStylesheet('/core/css/cms.css');
		
		$this->view->headScript()->appendFile('/core/js/admin/file.js');
    	
    	$this->view->folderNodes = Zend_Json::encode (Tg_Documents::getRootFolder ());
//		dump($this->view->folderNodes); die;	
    }
	
    public function testAction()
    {
		
		$this->view->headLink()->appendStylesheet('/core/css/cms.css');
		
//		$this->view->headScript()->appendFile('/core/js/admin/file.js');
    	
    	$this->view->folderNodes = Zend_Json::encode (Tg_Documents::getRootFolder ());
//		dump($this->view->folderNodes); die;	
    }
	
    public function convertFileAction()
    {
		$file = Tg_File::getFile($this->_getParam('fileId'));
		$result = Tg_File_Converter::convert($file, Tg_File_Converter::$FORCE_ALL);
		dump (Tg_File_Converter::getLog());
		
		die;
    }
	
    public function convertFilesAction()
    {
    	$table = new Tg_File_Db_Table_File ();
    	$select = $table->select()->where ("converted=''")->limit(10);
    	$files = $table->fetchAll($select);
    	foreach ($files as $file)
    	{
			Tg_File_Converter::clearLog();
			$result = Tg_File_Converter::convert($file);
			dump (Tg_File_Converter::getLog());
    	}
		
		die;
    }
    
    function folderAddAction ()
    {
    	$form = new Tg_File_Form_FolderAdd ();
		$requestData = $this->_request->getParams();
    	$this->view->form = $form;
		
		if ($this->_request->isPost()) {
			if($form->isValid($requestData)) {
				$formData = $form->getValues();
				
				unset($formData['submit']);
				
    			$folder = Tg_Documents::getFolderById ($this->_getParam('folder_id'));  
				
				Tg_Documents::addFolder($folder, $formData);
				
				$this->view->form = "Folder created";
				$this->view->refresh = true;
				
			} 
		} else
			$form->populate ($requestData);
    }
    
    function folderEditAction ()
    {
    	$form = new Tg_File_Form_FolderAdd ();
    	$form->addElement('hidden','id', array (
			'decorators' 	 => array('ViewHelper'),
		));
    	$form->removeElement ('folder_id');
    	$form->setAction('/admin/files/folder-edit');
		$requestData = $this->_request->getParams();
    	$folder = Tg_Documents::getFolderById ($this->_getParam('id')); 
    	$this->view->form = $form;
		
		if ($this->_request->isPost()) {
			if($form->isValid($requestData)) {
				$formData = $form->getValues();
				
				unset($formData['submit']);
				
				Tg_Documents::updateFolder($folder, $formData);
				
				$this->view->form = "Folder updated";
				$this->view->refresh = true;
			} 
		} else
			$form->populate ($folder->toArray());
    }
	
    public function folderViewAction()
    {
    	$this->view->folder = Tg_Documents::getFolderById ($this->_getParam('id'));    	
    }
	
    public function folderListAction()
    {
    	$this->view->rootNode = Tg_Documents::getRootFolder ();  	
    }
	
    public function folderDeleteAction()
    {
    	$folder = Tg_Documents::getFolderById ($this->_getParam('id'));  

    	Tg_Documents::deleteFolder($folder);
    	
    	die;
    }
    
    public function importAction ()
    {
    	$importDir = STORAGE_PATH.DIRECTORY_SEPARATOR.'incoming';  

    	if (!file_exists($importDir))
    	{
    		echo 'Import dir does not exist, please create: '. $importDir;
    		die;
    	}
		$filesImported = array();
    	$files = array ();
    	$folderId = $this->_getParam('folderId');	
    	$filesToImport = $this->_getParam('filesToImport');
    	
    	if (is_array($filesToImport))
    	{
//    		dump ($filesToImport);
			foreach ($filesToImport as $fileImport)
			{
				$filePath = $importDir.'/'.$fileImport;
				if (file_exists($filePath))
				{
					$fileObj = Tg_File::createFromFile($filePath,null,true);
				
		    		if ($folder = Tg_Documents::getFolderById($folderId))
		    		{
		    			$folder->addFile($fileObj);
		    		}
					
					$filesImported[] = $fileImport;
				}
			}
    	} 
    	else {
    	
    	}
    	    
    	$dirHandle = opendir($importDir);
    	
    	while (($file = readdir($dirHandle)) !== false)
    	{
    		if ($file != '..' && $file != '.') {
   				$filePath = $importDir.'/'.$file;
   				if (!is_dir($filePath)) {
	    			$fileObj = new stdClass();
	    			$fileObj->name = $file;
	    			$fileObj->path = $filePath;
	    			$fileObj->size = filesize($filePath);
	    			$files[]=$fileObj;    	
   				}		
    		}
    	}
    	
    	function cmp($a, $b)
		{
		    if ($a->name == $b->name) {
		        return 0;
		    }
		    return ($a->name < $b->name) ? -1 : 1;
		}
		
		usort($files, "cmp");
    	
    	$this->view->files = $files;
    	$this->view->filesImported = $filesImported;
    	$this->view->folderId= $folderId;
    	$this->view->incomingPath = realpath($importDir);
    }
}

