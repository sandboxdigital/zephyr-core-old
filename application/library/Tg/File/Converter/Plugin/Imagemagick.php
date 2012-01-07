<?php
class Tg_File_Converter_Plugin_Imagemagick extends Tg_File_Converter_Plugin_Abstract
{
	public $FILE_TYPES = array ('pdf','psd','tif','ai','eps');
	
	
	/**
	 * 
	 * Enter description here ...
	 * @param Tg_File_Db_File $file
	 */
	protected function _doConvert($file)
	{
		$image = new Tgx_ImageMagick ($file->getPath());
		$identify = $image->identify();
		if (is_array($identify)) 
		{
			Tg_File_Converter::log('Tgx_ImageMagick - identify worked');
			$counter = 0;

			$output = $file->getPath();
			$ext = $file->getExtension();
			$output = str_replace('.'.$ext, '.jpg', $output);
			if ($filePath = $image->convert ($counter, $output))
			{
				Tg_File_Converter::log('Tgx_ImageMagick - succeeded');
				Tg_File_Converter::log('Tgx_ImageMagick - output');
				Tg_File_Converter::log($image->output);
				return true;
			}
		} 

		Tg_File_Converter::log('Tgx_ImageMagick - failed');
		Tg_File_Converter::log('Tgx_ImageMagick - output');
		Tg_File_Converter::log($image->output);
		return false;
	}
}