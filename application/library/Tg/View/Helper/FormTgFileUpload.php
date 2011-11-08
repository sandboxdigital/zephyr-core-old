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
class Tg_View_Helper_FormTgFileUpload extends Zend_View_Helper_FormFile
{
    public function formTgFileUpload ($name, $value = null, $attribs = null)
    {

        $info = $this->_getInfo($name, $value, $attribs);

        extract($info); // name, id, value, attribs, options, listsep, disable

        // is it disabled?
        $disabled = '';
        if ($disable) {
            $disabled = ' disabled="disabled"';
        }

        // build the element
        $xhtml = '';
    	$url = "";

		if(isset($attribs['button'])) {
			$label = $attribs['button'];
			unset ($attribs['button']);
		}
		else {
			$label = "Select file";
		}

		if(isset($attribs['class'])) {
			$class = $attribs['class'];
			unset ($attribs['class']);
		}
		else {
			$class = $this->view->escape($name);
		}

		if(isset($attribs['callback'])) {
			$callback = $attribs['callback'];
			unset ($attribs['callback']);
		}
		else {
			$callback = $this->view->escape($name);
		}
		
		$xhtml .= '<input type="hidden" id="'.$this->cleanName($name).'" name="'.$name.'" value="' . $value . '" />';
		$xhtml .= '<table id="'.$this->cleanName($name).'_table">';
		$xhtml .= '</table>';
		$xhtml .= '<div><input id="'.$this->cleanName($name).'_btnUpload" type="button" class="'.$class.'_btnUpload btnUpload" value="'.$label.'" /></div>
		<script language="javascript" type="text/javascript">
			var swfupload_'.$this->cleanName($name).' = {
				hidden:"'.$this->cleanName($name).'",
				preview:"'.$this->cleanName($name).'_table",
				upload:"'.$this->cleanName($name).'_btnUpload"';
				
				foreach ($attribs as $key => $attrValue)
					$xhtml .= "\n,".$key.':"'.$attrValue.'"';
					
				$xhtml .= '
			};';
		
		$xhtml .= 'var tgFu = new Tg.FileUploadField (swfupload_'.$this->cleanName($name).');';
		
		if (!empty($value))
		{
			$file = Tg_File::getFileById($value);
			if ($file)
			{
				$xhtml .= 'tgFu.addFile ('.Zend_Json::encode($file->toObject()).');';
			}
		}
		$xhtml .= '</script>';


        return $xhtml;
    }
    
    public static function fileHtml ($name, $fileId )
    {
		$file = null;
		$xhtml = '';
		
        if (is_numeric($fileId)) {
        	$file = Tg_File::getFile ($fileId);
        }
        if ($file) {
        	$url = $file->getUrl('32x32');
        	$fileName = $file->name;
			
			$xhtml .= '<tr>';
			$xhtml .= '<td class="fileUploadThumbnail"><img src="'.$url.'"  /></td>';
			$xhtml .= '<td class="fileUploadName">'.$fileName.'</td>';
			$xhtml .= '<td class="fileUploadDelete"><a href="#" class="formFileUploadDelete">Remove this file</a>'; 
			$xhtml .= '</td>';
			$xhtml .= '</tr>';
	    }
		
		return $xhtml;
    }

	public function cleanName($name) {
		$filtered_name = '';
		for ($i=0;$i<strlen($name);$i++) {
			$current_char = substr($name,$i,1);
			if (ctype_alnum($current_char) == TRUE || $current_char == "_" || $current_char == ".") {
				$filtered_name .= $current_char;
			}
		}

		return $filtered_name;
	}
}
