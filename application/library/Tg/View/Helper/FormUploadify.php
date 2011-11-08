<?php

/**
 * Helper to generate a "file" element
 *
 * @category   Zend
 * @package    Zend_View
 * @subpackage Helper
 * @copyright  Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Tg_View_Helper_FormUploadify extends Zend_View_Helper_FormFile
{
//	public function getUploadPath ()
//	{
//		if (isset($this->_options['uploadPath']))
//			return $this->_options['uploadPath'];
//	}
	
    public function formUploadify($name, $value = null, $attribs = null)
    {
        $info = $this->_getInfo($name, $value, $attribs);

        extract($info); // name, id, value, attribs, options, listsep, disable

        // is it disabled?
        $disabled = '';
        if ($disable) {
            $disabled = ' disabled="disabled"';
        }

        $pathToUploadify = '/core/js/jquery.uploadify/';
        $pathToJquery = '/core/js/';
        
        $xhtml = '<div id="someID">xxxx</div>';
        
        $uploadPath = $this->getUploadPath ();
        
        $xhtml .=' 
<script type="text/javascript" src="'.$pathToUploadify.'swfobject.js"></script> 
<script type="text/javascript" src="'.$pathToUploadify.'jquery.uploadify.v2.1.0.min.js"></script>';
        
		$xhtml .= '<script type="text/javascript"> 
$(document).ready(function() { 
 $("#someID").uploadify({ 
  "uploader":  "'.$pathToUploadify.'uploadify.swf", 
  "script":    "'.$uploadPath.'", 
  "folder":    "'.$pathToUploadify.'uploads-folder", 
  "cancelImg": "'.$pathToUploadify.'cancel.png",
  "auto":true
 }); 
}); 
</script> ';

        return $xhtml;
    }
}
