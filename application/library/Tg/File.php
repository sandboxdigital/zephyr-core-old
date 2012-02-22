<?php
/**
 * Tg Site Factory Wrapper
 *
 * @copyright  Copyright (c) 2009 Thomas Garrood (http://www.garrood.com)
 */

class Tg_File
{
	protected static $_instance = null;

	protected $_table;
	protected $_storageFolder;
	protected $_tempFolder;
	protected $_cacheFolder;
	protected $_config;
	protected $_defaultConfig = array (
		'enableLocationChecks'						=>true,
		'storageFolder' 							=> "",
		'cacheFolder'		 						=> "/file",
		'urlPre'                                    =>''
	);

	public function __construct() 
	{
		$this->_table = new Tg_File_Db_Table_File();

		$config = Zend_Registry::getInstance()->get('siteconfig');
		
		$this->_defaultConfig['storageFolder'] = Zeph_Config::getPath('%PATH_STORAGE%/uploads');
		$this->_defaultConfig['tempFolder'] = Zeph_Config::getPath('%PATH_STORAGE%/tmp');
		$this->_defaultConfig['cacheFolder'] = Zeph_Config::getPath('%PATH_PUBLIC%/file');
		
		if (isset($config['file']))
			$this->_config  = $config['file']+$this->_defaultConfig;
		else
			$this->_config  = $this->_defaultConfig;

		if (empty($this->_config['urlPre']))
		{
			$this->_config['urlPre'] = Zend_Controller_Front::getInstance()->getBaseUrl();
		}

		$this->_storageFolder = Zeph_Config::getPath($this->_config['storageFolder']);
		$this->_tempFolder = Zeph_Config::getPath($this->_config['tempFolder']);
		$this->_cacheFolder = Zeph_Config::getPath($this->_config['cacheFolder']);

		if ((isset($this->_config['enableLocationChecks'])) && $this->_config['enableLocationChecks']==true) {
			// create storage dir if it doesn't exist
			if(!is_dir($this->_storageFolder)) {
				@mkdir($this->_storageFolder);
				if(!is_dir($this->_storageFolder))
					throw new Zend_Exception('File storage folder not found and could not be created: '.$this->_config['storageFolder']);
			}
				
			if (!is_writable($this->_storageFolder))
				throw new Zend_Exception('File storage folder isn\'t writable: '.$this->_storageFolder);

			if (!is_dir($this->_cacheFolder))
				throw new Zend_Exception('Image cache doesn\'t exist: '.$this->_cacheFolder);
				
			if (!is_writable($this->_cacheFolder))
				throw new Zend_Exception('Image cache isn\'t writable: '.$this->_cacheFolder);
		}
	}

	/**
	 * Get singleton instance
	 *
	 * @return  Tg_File $instance
	 */
	public static function getInstance() {
		if(self::$_instance === null) {
			self::$_instance = new self();
		}
		return self::$_instance;
	}
	
	/**
	 * Get file by id
	 * 
	 * @param $id
	 * @return Tg_File_Db_File
	 */

	public static function getFile ($id = 0)
	{
		return self::getFileById($id);
	}
	

	public static function getOption ($id)
	{
		$inst = self::getInstance();
		if (isset($inst->_config[$id]))
			return $inst->_config[$id];
		else
			return false;
	}
	
	/**
	 * Get file by id
	 * 
	 * @param $id
	 * @return Tg_File_Db_File
	 */
	public static function getFileById ($id = 0)
	{
		if ($id>0) {
			$inst = self::getInstance();
			$select = $inst->_table->select()->where('id = ?', $id);
			return $inst->_table->fetchRow($select);
		} else
			return null;
	}

	
	/**
	 * Get file by name
	 * 
	 * @param $id
	 * @return Tg_File_Db_File
	 */
	public static function getFileByName ($name)
	{
		
		$fileNameInfo = pathinfo($name);
	
		$parts = explode ('_',$fileNameInfo['filename']);
		
		$identifier = $parts[count($parts)-1];
		$size = substr($identifier,14);
		$identifier = substr($identifier,0,13);
		
		if (strlen($identifier)==13) {
			$inst = self::getInstance();
			$select = $inst->_table->select()->where('identifier = ?', $identifier);
			$file = $inst->_table->fetchRow($select);
			if ($file) {
				if (strlen($size)>0) {
					$file->size = $size;
				}
			}	
			return $file;
		} else
			throw new Zend_Exception('Could not get identifier - wrong filename format');
//		} else
//			return null;
	}

	public static function getStorageFolder ()
	{
		$inst = self::getInstance();
		return $inst->_storageFolder;
	}

	public static function getCacheFolder ()
	{
		$inst = self::getInstance();

		return $inst->_cacheFolder;
	}

	public static function getSizes ()
	{
		$inst = self::getInstance();

		return $inst->_config['image']['size'];
	}

	/**
	 * Uploads a file to the server and returns a Tg_File_Db_File representing the file
	 *
	 * @return Tg_File_Db_File $file
	 */
	public static function createFromUpload ($name)
	{
		$inst = self::getInstance();

		if (isset($_FILES[$name])) {
			if ($_FILES[$name]['error']==0) {
				if (file_exists($_FILES[$name]['tmp_name'])) {
					// 
					$file = 				$inst->_table->createRow();
					$file->name 			= self::cleanFilename($_FILES[$name]['name']);
					$file->identifier 		= self::identifier();
					$file->fullname 		= self::fullname($file->name, $file->identifier);
					$file->type 			= $_FILES[$name]['type'];
						
					$fullName = $file->fullname;
					$tempFile = $_FILES[$name]['tmp_name'];
					
					if (!is_file($tempFile)) {
						throw new Zend_Exception('Uploaded file not found when trying to save.');
					}
			
					if (is_file($inst->_storageFolder.'/'.$fullName)) {
						throw new Zend_Exception('File ('.$fullName.') already exists! ');
					}		
			
					if(!@move_uploaded_file($tempFile, $inst->_storageFolder.'/'.$fullName)) {
						echo 'Could not save file to storage<br />';
						echo "Folder: ".$inst->_storageFolder."<br />";
						echo "Dest file: ".$inst->_storageFolder.'/'.$fullName."<br />";
						echo "Src file: ".$tempFile."<br />";
						die;
						throw new Zend_Exception('Could not save file to storage.');
					}
			
					$file->save ();
					return $file;
					
				} else {
					print_r ($_FILES);
					return $_FILES[$name]['tmp_name'].' does not exist';
				}
			} else {
				switch ($_FILES[$name]['error']) {
			        case UPLOAD_ERR_INI_SIZE:
			            return 'The uploaded file exceeds the upload_max_filesize directive in php.ini';
			        case UPLOAD_ERR_FORM_SIZE:
			            return 'The uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the HTML form';
			        case UPLOAD_ERR_PARTIAL:
			            return 'The uploaded file was only partially uploaded';
			        case UPLOAD_ERR_NO_FILE:
			            return 'No file was uploaded';
			        case UPLOAD_ERR_NO_TMP_DIR:
			            return 'Missing a temporary folder';
			        case UPLOAD_ERR_CANT_WRITE:
			            return 'Failed to write file to disk';
			        case UPLOAD_ERR_EXTENSION:
			            return 'File upload stopped by extension';
			        default:
			            return 'Unknown upload error';
			    } 
			}
		} else {
			$return = $name.' does not contain data';
			return $return;
		}
	}

	/**
	 * Uploads a file to the server and returns a Tg_File_Db_File representing the file
	 *
	 * @return Tg_File_Db_File $file
	 */
	
	public static function createFromValumsUpload ($name)
	{	
		require_once dirname(__FILE__).'/File/Classes.php';

		$inst = self::getInstance();
		
		// list of valid extensions, ex. array("jpeg", "xml", "bmp")
		$allowedExtensions = array();
		// max file size in bytes
		//$sizeLimit = 150 * 1024 * 1024;
		
		$uploader = new qqFileUploader($allowedExtensions);
		$result = $uploader->handleUpload($inst->_tempFolder.'/');
		
		if (isset($result['error'])) {
//			$result['error'] = $inst->_tempFolder.'/'; 
			return $result;
		} else 
		{
			$file = self::createFromFile($result['path']);
			return $file;
		}
		
	}
	
	
	
	/**
	 * Returns a Tg_File_Db_File representing of a file on the filesystem, moves the file to the storage folder
	 *
	 * @return Tg_File_Db_File $file
	 */
	public static function createFromFile ($path, $name = null, $deleteAfterCreate = true)
	{
		$inst = self::getInstance();
		
		if (!$name) {
			$path_parts = pathinfo ($path);
			$name = $path_parts['filename'].'.'.$path_parts['extension'];
		}

		$file = 				$inst->_table->createRow();
		$file->name 			= self::cleanFilename($name);
		$file->identifier 		= self::identifier();
		$file->fullname 		= self::fullname($file->name, $file->identifier);
		$file->type 			= '';
				
		$fullName = $file->fullname;
		$tempFile = $path;
		
		if (!is_file($tempFile)) {
			throw new Zend_Exception('Uploaded file not found when trying to save.');
		}

		if (is_file($inst->_storageFolder.'/'.$fullName)) {
			throw new Zend_Exception('File ('.$fullName.') already exists! ');
		}		
		
		if ($deleteAfterCreate)
			$return = @rename($tempFile, $inst->_storageFolder.'/'.$fullName);
		else
			$return = @copy($tempFile, $inst->_storageFolder.'/'.$fullName);
		
		if(!$return) {
			echo 'Could not save file to storage<br />';
			echo "Folder: ".$inst->_storageFolder."<br />";
			echo "Dest file: ".$inst->_storageFolder.'/'.$fullName."<br />";
			echo "Src file: ".$tempFile."<br />";
			die;
			throw new Zend_Exception('Could not save file to storage.');
		}

		$file->save ();
		return $file;
	}

	public static function identifier ()
	{
		return uniqid();
	}
	
	public static function cleanFilename ($filename)
	{
		$filename = preg_replace("/[^a-z0-9-\.]/", "-", strtolower($filename));
		
		return $filename;
	}
	
	public static function fullname ($filename, $id)
	{
		$info = pathinfo($filename);
		
		$filename = $info['filename'].'_'.$id;
		
		if ($info['extension'])
			$filename.='.'.$info['extension'];
		
		return $filename;
	}
}


