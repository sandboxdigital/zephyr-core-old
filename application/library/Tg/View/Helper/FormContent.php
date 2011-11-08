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
class Tg_View_Helper_FormContent extends Zend_View_Helper_FormFile
{
    public function formContent($name, $value = null, $attribs = null)
    {
        $info = $this->_getInfo($name, $value, $attribs);

        extract($info); // name, id, value, attribs, options, listsep, disable

        // is it disabled?
        $disabled = '';
        if ($disable) {
            $disabled = ' disabled="disabled"';
        }
		
        $xhtml = '';
        
		$options = array (
			'subform'=>true,
			'name' =>$name
//			'url'=>'/admin/content/save',
//			'hidden'=>array (
//			'pageId'=>$pageId
//			)
		);
		
    	$xhtml = Tg_Content::getInstance()->getFormFromFile ($attribs['form'], $value, $options);
    	$xhtml .= $this->_hidden($name,'',array('id'=>$name));
    	
        return $xhtml;
    }
}
