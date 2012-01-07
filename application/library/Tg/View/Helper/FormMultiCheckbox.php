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
class Tg_View_Helper_FormMultiCheckbox extends Zend_View_Helper_FormMultiCheckbox
{

	public function formMultiCheckbox($name, $value = null, $attribs = null,
	$options = null, $listsep = "<br />\n")
	{
		$xhtml = '';
		if (is_array(next($options))) {
			foreach ($options as $optionLabel => $optionGroup) {
				$xhtml .= '<div class="radioGroup"><b>'.$optionLabel.'</b>'.$listsep;
				$xhtml .= $this->formRadio($name, $value, $attribs, $optionGroup, $listsep);
				$xhtml .= '</div>';
			}
			$xhtml .= '<div style="clear:both;height:0px;line-height:0px"></div>';
		}else
			$xhtml.=$this->formRadio($name, $value, $attribs, $options, $listsep);
			
		return $xhtml;

	}
}