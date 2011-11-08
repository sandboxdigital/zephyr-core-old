<?php
class Tg_File_Utils
{
	public static function getThumbnailImg ($file)
	{
		$url = self::getThumbnailUrl($file);
		
		
		return '<img src="'.$url.'" width="48" height="48" />';		
	}
	
	public static function getThumbnailUrl ($file)
	{
		$ext = '';
		
		if ($file instanceof Tg_File_Db_File) {
			$thumbnailExts = array ('jpg','jpeg','png','gif');
			
			$ext = self::getExtension($file->name);
			
			if (in_array($ext, $thumbnailExts))
				return $$file->getUrl('thumbnail');
		} else 
		{
			$ext = self::getExtension($file);
		}

		if ($ext == '')
			$ext = 'default';
		
		return '/core/images/fileicons/'.$ext.'.png';
	}

	public static function getExtension ($name) {
		$pathinfo = pathinfo($name);
		
		if (isset($pathinfo['extension']))
			return strtolower($pathinfo['extension']);
		else 
			return '';
	}
	/**
	 * getFileName(WithoutExtension) returns filename minus extension
	 * Enter description here ...
	 */
	public static function getFileName ($name) {
		$pathinfo = pathinfo($name);
		
		if (isset($pathinfo['filename']))
			return strtolower($pathinfo['filename']);
		else 
			return '';
	}

	/**
	 * Determines if a file is an image

	 * @param string $path
	 * @return bool 
	 */
	public static function isImage($path) 
	{	
		$image_info = getimagesize($path);
		if($image_info[2] < 1 || $image_info[2] > 3) {
			return false;
		}
		else {
			return true;
		}		
	}
	
	public static function getImageSize ($name)
	{	
		$fileNameInfo = pathinfo($name);
		$parts = explode ('_',$fileNameInfo['filename']);
		
		$identifier = $parts[count($parts)-1];
		$size = substr($identifier,14);
		$identifier = substr($identifier,0,13);
		
		return $size;
	}
	
	public static function getFileSize($file, $raw = false) 
	{
		if ($file instanceof Tg_File_Db_File)
			$filePath = $file->path;
		else 
			$filePath = $file;
		
		$rawSize = filesize($filePath);
		
		if ($raw)
			return $rawSize;
		else
			return self::_formatBytes ($rawSize);
	}
	
	private static function _formatBytes($bytes, $precision = 2) {
	    $units = array('B', 'KB', 'MB', 'GB', 'TB');
	  
	    $bytes = max($bytes, 0);
	    $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
	    $pow = min($pow, count($units) - 1);
	  
	    $bytes /= pow(1024, $pow);
	  
	    return round($bytes, $precision) . ' ' . $units[$pow];
	}
}