<?php

/**
 * Helper to generate a "img" tag element
 *
 * @category Zend
 * @packageZend_View
 * @subpackage Helper
 * @copyrightCopyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @licensehttp://framework.zend.com/license/new-bsd New BSD License
 */
class Tg_View_Helper_Img extends Zend_View_Helper_FormFile
{
	public function img($file, $size = '', $attribs = array ())
	{
		$url = '';
		$xhtml = '';
		
		if ($file != null)
		{
			if (is_numeric ($file))
				$file = Tg_File::getFileById($file);	
			
			if ($file instanceof Tg_Content_Element_File)
			{
				$file = $file->getFile();	
			}
			
			if ($file instanceof Tg_File_Db_File)
			{
//				$xhtml = $file->getImg($size, $options);
				$url = 	$file->getImageUrl($size);
			}
			if (is_string($file)) 
			{
				$url = $file;
			}			
		}
		
		if ($url == '' && isset($attribs['notFound']))
		{
			$url = $attribs['notFound'];
		}
		
		if (!empty($url))
		{
			if (isset($attribs['notFound']))
				unset ($attribs['notFound']);
			
			// XHTML or HTML end tag?
			$endTag = ' />';
			if (($this->view instanceof Zend_View_Abstract) && !$this->view->doctype()->isXhtml())
				$endTag= '>';
		
			$xhtml = '<img src="'.$url.'" '
				. $this->_htmlAttribs($attribs)
				. $endTag;
		}
		
		return $xhtml;
	}
}
