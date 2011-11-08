<?php
class Tg_File_Converter_Plugin_Abstract
{
	public $FILE_TYPES = array ();

	/**
	 * 
	 * Enter description here ...
	 * @param Tg_File_Db_File $file
	 */
	public function canConvert ($file)
	{
		if ($file instanceof Tg_File_Db_File)
		{
			$ext = $file->getExtension();
			if (in_array($ext, $this->FILE_TYPES))
			{
				Tg_File_Converter::log($ext.' found in types: '.implode(',', $this->FILE_TYPES));
				return true;
			} else 
				Tg_File_Converter::log($ext.' not found in types: '.implode(',', $this->FILE_TYPES));
		} else 
			Tg_File_Converter::log('File not a Tg_File');
		
		// wasn't converted
		return false;
	}
	
	/**
	 * 
	 * Enter description here ...
	 * @param Tg_File_Db_File $file
	 */
	public function convert ($file)
	{
		return $this->_doConvert($file);
	}
	
	/**
	 * 
	 * Enter description here ...
	 * @param Tg_File_Db_File $file
	 */
	protected function _doConvert ($file)
	{
		return true;
	}
}