<?php
/**
 * File Class
 *
 * @copyright  Copyright (c) 2008 Delete Limited (http://www.deletelondon.com)
 */

class Tg_File_Db_File extends Tg_Db_Table_Row_Crud
{
	protected $_size 				= 'original';	
	protected $_previewExtension	= 'original';	
	protected $_tableClass 			= 'Tg_File_Db_Table_File';
	protected $_folder 				= '';
	protected $_handle;
	
	public function __toString () 
	{
		return 'id:'.$this->id .', name:'.$this->fullname;
	}
	
	public function __set ($name, $value) 
	{
		if ($name == 'size')
			$this->_size = $value;
		else if ($name == 'previewExtension')
			$this->_previewExtension = $value;
		else
			parent::__set($name, $value);
	}
	
	public function __get ($name) 
	{
		if ($name == 'size')
			return $this->_size;
		elseif ($name == 'path')
			return Tg_File::getStorageFolder().'/'.$this->fullname;
		elseif ($name == 'url')
			return $this->getUrl();
		else
			return parent::__get($name);
	}

	public function getUrl($size = null) 
	{
		return Tg_File::getOption('urlPre').'/file/'.$this->_fullnameWithSize($size);
	}
	
	public function getDownloadUrl() 
	{
		return Tg_File::getOption('urlPre').'/file/download/name/'.$this->fullname;
	}

	public function getImg($size = null) 
	{
		return '<img src="'.$this->getUrl($size).'" />';
	}
	
	public function getPath ($size = null)
	{
		return Tg_File::getStorageFolder().'/'.$this->_fullnameWithSize($size);
	}
	
	public function getCachePath ($size = null)
	{
		return Tg_File::getCacheFolder().'/'.$this->_fullnameWithSize($size);
	}
	
	public function size($raw = false) 
	{
		return Tg_File_Utils::getFileSize($this, $raw);
	}
	
	public function getImageSize ()
	{
		return getimagesize($this->getPath());
	}

	public function getExtension ($name = '') {
		if (!$name)
			$name = $this->_data['name'];
		
		$pathinfo = pathinfo($name);
		
		if (isset($pathinfo['extension']))
			return strtolower($pathinfo['extension']);
		else 
			return '';
	}
	
	public function open ($mode = 'r')
	{
		$this->_handle = fopen ($this->path, $mode);
		return $this->_handle;
	}
	
	public function close ()
	{
		fclose ($this->_handle);
	}
	
	public function getCSVData ($skipFirstRow = true)
	{
		ini_set('auto_detect_line_endings', true);

		$fileHandle = $this->open();
		$rows = array();
		while (($data = fgetcsv($fileHandle, 0, ",")) !== FALSE) {
			
			if ($skipFirstRow) {
				// skip
				$skipFirstRow = false;
			} else
				$rows[] = $data;
		}

		$this->close ();
		
		return $rows;
	}
	
	/**
	 * Delete the file
	 * 
	 * removes the database record and all files from file system
	 */
	
	public function delete() 
	{
		$this->_table->delete ('id='.$this->id);
	}
	

	/**
	 * Display can request a different file type to actual file, eg .jpg or .flv
	 * 
	 * Enter description here ...
	 * @param string $requestName
	 * @throws Zend_Exception
	 */
	public function display($ext='', $size='') 
	{
		if ($ext == '')
			$ext = $this->getExtension();
		
		$storageFolder = Tg_File::getStorageFolder();
		$cacheFolder = Tg_File::getCacheFolder();
		
		$requestName = Tg_File_Utils::getFileName($this->fullname).'.'.$ext;
		$requestPath = $storageFolder.'/'.$requestName;
		
		if(!is_file($requestPath))
		{
			$requestName = $this->fullname;
			$requestPath = $storageFolder.'/'.$requestName; // request fallback to actual file
		}
		
		if(!is_file($requestPath))
			throw new Zend_Exception('File not found: '.$this->fullname); // file not found!
				
		if(Tg_File_Utils::isImage($requestPath)) 
		{
			if($size == 'original' || $size == '') {
				copy($requestPath, $cacheFolder.'/'.$requestName);
				header('Location: '.$_SERVER['REQUEST_URI'].'?reload');
				die();
			}
			else {		
				if($this->_resizeSave($requestName, $size)) {		
					header('Location: '.$_SERVER['REQUEST_URI'].'?reload');
					die();
				}
			}
			
		}
		else {
			if($size == 'thumbnail') {
				$url = Tg_File::getOption('urlPre').'/core/images/fileicons/'.$this->getExtension().'.png';
				header('Location: '.$url.'?reload');
				die();
			}
			else {
				copy($storageFolder.'/'.$requestName, $cacheFolder.'/'.$requestName);
				header('Location: '.$_SERVER['REQUEST_URI'].'?reload');
				die();
			}
		}

		// something aweful happened ...
		return false;
	}
	
	public function toObject ()
	{
		$o = new stdClass();
		$o->id = $this->id;
		$o->name = $this->name;
		$o->fullname = $this->fullname;
		$o->type = $this->type;
		$o->url = $this->getUrl();
		$o->thumbnailUrl = $this->getThumbnailUrl();

		return $o;
	}
	
	public function toJson ()
	{
		return $this->toObject ();
	}
	
	public function getThumbnailUrl ()
	{
		$url = $this->getImageUrl('thumbnail');
		if ($url == '') {
			// no thumbnail image of file .. return an icon
			$ext = $this->getExtension();
			if ($ext == '')
				return Tg_File::getOption('urlPre').'/core/images/fileicons/default.png';
			else 
				return Tg_File::getOption('urlPre').'/core/images/fileicons/'.$ext.'.png';
		} else 
			return $url;
	}

	public function getImageUrl($size) 
	{
		$storageFolder = Tg_File::getStorageFolder();
		
		$ext = $this->getExtension();
		
		$thumbnailExts = array ('jpg','jpeg','png','gif');
		if (!in_array($ext, $thumbnailExts))
		{
			$sourceName = $this->_fullnameWithSize('','jpg');
			$sourceSizedName = $this->_fullnameWithSize($size,'jpg');
		}
		else {
			$sourceName = $this->_fullnameWithSize('');
			$sourceSizedName = $this->_fullnameWithSize($size);
		}
		
		$sourcePath = $storageFolder.'/'.$sourceName;

		if (file_exists($sourcePath))
		{
			return Tg_File::getOption('urlPre').'/file/'.$sourceSizedName;
		} else 
			return '';
	}
	

	private function _resizeSave ($sourceName, $variant, $destName = '') 
	{
		$storageFolder = Tg_File::getStorageFolder();
		$cacheFolder = Tg_File::getCacheFolder();
		$sizes = Tg_File::getSizes ();
		
		// get the variant options for the resizeSave
		if(isset($sizes[$variant])) {
			$width = $sizes[$variant]['width'];
			$height = $sizes[$variant]['height'];
			if(isset($sizes[$variant]['options'])) {
				$options = $sizes[$variant]['options'];
			}
			else {
				$options = null;
			}
		}
		else {
			$details = explode ('x',$variant);
			
			if ((count($details)>0) && is_numeric($details[0]) && is_numeric($details[1])) {
				$width = $details[0];
				$height = $details[1];
				$options = array ('crop'=>true);
			} else
				throw new Zend_Exception('Resize variant not found in config');
		}

		$sourcePath = $storageFolder.'/'.$sourceName;

		// setup where the new resized file should be stored
		if ($destName == '')
		{
			$destFileName = Tg_File_Utils::getFileName($sourceName);
			$destExt = Tg_File_Utils::getExtension($sourceName);
			$destName = $destFileName.'-'.$variant.'.'.$destExt;
		}
		
		$destPath = $cacheFolder.'/'.$destName;

		// resize the file to the correct location
		$Image = new Tg_File_Image ($sourcePath);
		$Image->resize($width, $height, $destPath, $options);

		if(is_file($destPath)) {
			return true;
		}
		else {
			return false;
		}
	}

	private function _fullnameWithSize ($size = '', $ext='') {
		$name = $this->_data['name'];
		
		$pathinfo = pathinfo($name);
		$filename = $pathinfo['filename'];
		if ($ext == '' && isset($pathinfo['extension']))
			$ext = $pathinfo['extension'];
		$identifier = $this->_data['identifier'];
		
		if($size == '') {
			$size = 'original';
		}
		
		if ($ext != '')
			$ext='.'.$ext;

		if($size && $size != 'original') {
			$fullname = $filename.'_'.($identifier).'-'.$size.$ext;
		}
		else {
			$fullname = $filename.'_'.($identifier).$ext;
		}

		return $fullname;
	}
}