<?php
class Tg_View_Helper_FormCustom extends Zend_View_Helper_FormElement
{
    public function formCustom($name, $value = null, $attribs = null)
    {
        $info = $this->_getInfo($name, $value, $attribs);
        extract($info); // name, value, attribs, options, listsep, disable

        $xhtml = "";
		if(isset($attribs['html'])) {
			$xhtml .= $attribs['html'];
			unset($attribs['html']);
		}

        return $xhtml;
    }
}
