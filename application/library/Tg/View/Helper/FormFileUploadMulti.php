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
class Tg_View_Helper_FormFileUploadMulti extends Zend_View_Helper_FormText
{
	public function formFileUploadMulti ($name, $value = null, $attribs = null,
	$options = null, $listsep = "<br />\n")
	{
		$info = $this->_getInfo($name, $value, $attribs, $options, $listsep);
		extract($info); // name, id, value, attribs, options, listsep, disable

		// force $value to array so we can compare multiple values to multiple
		// options; also ensure it's a string for comparison purposes.
		$value = array_map('strval', (array) $value);
	

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

		$xhtml .= '<table id="'.$this->cleanName($name).'_table">';
		if (is_array($value)) {
			foreach ($value as $fileId ){
				$xhtml .= Tg_View_Helper_FormFileUpload::fileHtml($name, $fileId);
			}
		}
		$xhtml .= '</table>';
		$xhtml .= '<div id="'.$this->cleanName($name).'_progress"></div>';
		$xhtml .= '<div> 
<input id="'.$this->cleanName($name).'_btnUpload" type="button" class="'.$class.'_btnUpload btnUpload" value="'.$label.'" />
<input id="'.$this->cleanName($name).'_btnCancel" type="button" class="'.$class.' _btnCancel btnCancel" value="Cancel upload" style="display:none;" />
</div>
<script language="javascript" type="text/javascript">

$(document).ready(function() {
	var swfupload_'.$this->cleanName($name).' = {
		name:"'.$this->cleanName($name).'",
		progress:"#'.$this->cleanName($name).'_progress",
		preview:"#'.$this->cleanName($name).'_table",
		upload:"#'.$this->cleanName($name).'_btnUpload",
		cancel:"#'.$this->cleanName($name).'_btnCancel",
		mode:"multiple",
		callback:"swfUploadCallback_'.$callback.'"';
		
		foreach ($attribs as $key => $value)
			$xhtml .= "\n,".$key.':"'.$value.'"';
			
		$xhtml .= '
	}

	$.swfupload.addField(swfupload_'.$this->cleanName($name).');
});
</script>';


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
