<?php
class Tg_File_Converter_Plugin_Ffmpeg extends Tg_File_Converter_Plugin_Abstract
{
	public $FILE_TYPES = array ('mov','mpg','mpeg','wmv','mp4');
	
	protected function _doConvert($file)
	{
		$result = array ();
		Tg_File_Converter::log('Ffmpeg 2');
		$outVideo = $file->getPath();
		$ext = $file->getExtension();
		$outVideo = str_replace('.'.$ext, '.flv', $outVideo);
		if (Tgx_Ffmpeg::convertToFLV($file->getPath(), $outVideo))
		{
			Tg_File_Converter::log('Ffmpeg - succeeded');
			Tg_File_Converter::log('Ffmpeg - output');
			Tg_File_Converter::log(Tgx_Ffmpeg::$output);
			@mail('thomas.garrood@gmail.com','FFMPEG SUCCEED',Tgx_Ffmpeg::$output);
			return true;
		} else 
		{
			Tg_File_Converter::log('Ffmpeg - failed');
			Tg_File_Converter::log('Ffmpeg - output');
			Tg_File_Converter::log(Tgx_Ffmpeg::$output);
			@mail('thomas.garrood@gmail.com','FFMPEG fail',Tgx_Ffmpeg::$output);
			return false;
		}
		return $result;
	}
}