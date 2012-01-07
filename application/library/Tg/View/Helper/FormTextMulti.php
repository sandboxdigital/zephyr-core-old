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
class Tg_View_Helper_FormTextMulti extends Zend_View_Helper_FormText
{
	public function formTextMulti ($name, $value = null, $attribs = null,
	$options = null, $listsep = "<br />\n")
	{
		$info = $this->_getInfo($name, $value, $attribs, $options, $listsep);
		extract($info); // name, id, value, attribs, options, listsep, disable

		// force $value to array so we can compare multiple values to multiple
		// options; also ensure it's a string for comparison purposes.
		$value = array_map('strval', (array) $value);

		$selectBase = $this->_select ($name, $id, $attribs, '');
		
		$xhtml = "";
		$selects = "";
		
		if (count($value)>0) {
			foreach ($value as $key => $option) {
				$selects .= $this->_select ($name, $id, $attribs, $option);
			}
		} else {
			$selects = $selectBase;
		}

		$xhtml = "<div class=\"selects\">".$selects."</div><div><a href=\"#\" class=\"add\">Add more</a></div>";
		
		$xhtml .= '
<script language="javascript" type="text/javascript">
(function($) {
	$(document).ready(function() {
		$("#'.$this->view->escape($id).'-element a.add").live ("click", function() {
	    	$(\'#'.$this->view->escape($id).'-element div.selects\').append (\''.$selectBase.'\');
		    hideRemoves ();
	    	return false;
	    });
	    
		$("#'.$this->view->escape($id).'-element a.remove").live ("click",function(){
	    	$(this).parent().remove();
	    	hideRemoves ();
	    	return false;
	    });
	    
	    hideRemoves ();
	});
	    
	function hideRemoves () 
	{
		c("#'.$this->view->escape($id).'-element div.selects div.select")
		c($("#'.$this->view->escape($id).'-element div.selects div.select").length)
	    if($("#'.$this->view->escape($id).'-element div.selects div.select").length<2) {
	    	$("#'.$this->view->escape($id).'-element div.selects div.select a.remove").hide();
	    } 
	    else {
	    	$("#'.$this->view->escape($id).'-element div.selects div.select a.remove").show();
	    }
	}
})(jQuery);
</script>';

		return $xhtml;
	}

	function _select ($name, $id, $attribs, $value)
	{
		// Build the surrounding select element first.
		$xhtml = '<div class="select"><input '
		. ' name="' . $this->view->escape($name) . '"'
		. ' value="' . $this->view->escape($value) . '"'
		. ' class="textMedium"'
		. ' id="'.$this->view->escape($id).'"'
		. $this->_htmlAttribs($attribs)
		. " /><a href=\"#\" class=\"remove\">-</a></div>";

		return $xhtml;
	}
}
