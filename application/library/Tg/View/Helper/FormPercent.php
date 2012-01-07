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
class Tg_View_Helper_FormPercent extends Zend_View_Helper_FormText
{
	public function formPercent ($name, $value = null, $attribs = null,
	$options = null, $listsep = "<br />\n")
	{
		$info = $this->_getInfo($name, $value, $attribs, $options, $listsep);
		extract($info); // name, id, value, attribs, options, listsep, disable
		
		// force $value to array so we can compare multiple values to multiple
		// options; also ensure it's a string for comparison purposes.
		$value = array_map('strval', (array) $value);
		
		$xhtml = "";
		
		foreach ($options as  $key => $option) {
			$ivalue = isset($value[$key])?$value[$key]:0;
			$xhtml .= $this->_select ($name, $id, $attribs, $key, $option, $ivalue);
		}

		return $xhtml;
	}

	function _select ($name, $id, $attribs, $key, $option, $value='')
	{
		$xhtml = '<div class="percentRow"><input type="text" class="textTiny" value="'.$value.'" id="'.$this->view->escape($id.'-'.$key).'" name="'.$this->view->escape($id).'['.$key.']" /><span class="description">'.$option.'</span></div>';

		return $xhtml;
	}
}
