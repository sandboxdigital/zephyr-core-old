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
class Tg_View_Helper_FormSelectMulti extends Zend_View_Helper_FormSelect
{
	public function formSelectMulti ($name, $value = null, $attribs = null,
	$options = null, $listsep = "<br />\n")
	{
		$info = $this->_getInfo($name, $value, $attribs, $options, $listsep);
		extract($info); // name, id, value, attribs, options, listsep, disable

		// force $value to array so we can compare multiple values to multiple
		// options; also ensure it's a string for comparison purposes.
		$value = array_map('strval', (array) $value);

		$selectBase = $this->_select ($name, $id, $attribs, $options, array ());
		
		$xhtml = "";
		$selects = "";
		
		if (count($value)>0) {
			foreach ($value as $key => $option) {
				$selects .= $this->_select ($name, $id, $attribs, $options, array ($option));
			}
		} else {
			$selects = $selectBase;
		}

		$xhtml = "<div class=\"selects\">".$selects."</div><div><a href=\"#\" class=\"add\">Add more</a></div>";
		
		$xhtml .= '
<script language="javascript" type="text/javascript">
(function($) { // hide the namespace
	
	$(document).ready(function() {
		$("#'.$this->view->escape($id).'-element a.add").live ("click",function(){
	    	var s = $(\'#'.$this->view->escape($id).'-element div.selects\').append (\''.$selectBase.'\').find("select:last");
	    	s.sSelect();
		    hideRemoves ();
			if (window.dirty != undefined)
				window.dirty = true;
	    	return false;
	    });
	    
		$("#'.$this->view->escape($id).'-element a.remove").live ("click",function() {
	    	$(this).parent().remove();
	    	hideRemoves ();
			if (window.dirty != undefined)
				window.dirty = true;
	    	return false;
	    });
	    
	    hideRemoves ();
	    
		$("#'.$this->view->escape($id).'-element div.selects select").sSelect();
	    
	});
	    
	function hideRemoves () {
	    if($("#'.$this->view->escape($id).'-element div.selects div.select").length<2) {
	    	$("#'.$this->view->escape($id).'-element div.selects div.select a.remove").hide();
	    } else {
	    	$("#'.$this->view->escape($id).'-element div.selects div.select a.remove").show();
	    }
	}
})(jQuery);
</script>';

		return $xhtml;
	}

	function _select ($name, $id, $attribs, $options, $value)
	{
		// Build the surrounding select element first.
		$xhtml = '<div class="select"><select'
		. ' name="' . $this->view->escape($name) . '"'
		. ' id="' . $this->view->escape($id) . '"'
		. $this->_htmlAttribs($attribs)
		. ">";

		// build the list of options
		$list       = array();
		$translator = $this->getTranslator();
		foreach ((array) $options as $opt_value => $opt_label) {
			if (is_array($opt_label)) {
                if (null !== $translator) {
                    $opt_value = $translator->translate($opt_value);
                }
                
                $list[] = '<optgroup'
                        . ' label="' . $this->view->escape($opt_value) .'">';
                foreach ($opt_label as $val => $lab) {
                    $list[] = $this->_build($val, $lab, $value, false);
                }
                $list[] = '</optgroup>';
            } else {
				$list[] = $this->_build($opt_value, $opt_label, $value, false);
            }
		}

		// add the options to the xhtml and close the select
		$xhtml .= implode("", $list) . "</select> <a href=\"#\" class=\"remove\">-</a></div>";

		return $xhtml;
	}
}
