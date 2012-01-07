<?php
class Tg_View_Helper_FormTinyMce extends Zend_View_Helper_FormTextarea
{
	public function formTinyMce($name, $value = null, $attribs = null)
	{
 		$info = $this->_getInfo($name, $value, $attribs);
        extract($info); // name, value, attribs, options, listsep, disable

        // is it disabled?
        $disabled = '';
        if ($disable) {
            // disabled.
            $disabled = ' disabled="disabled"';
        }

        // Make sure that there are 'rows' and 'cols' values
        // as required by the spec.  noted by Orjan Persson.
        if (empty($attribs['rows'])) {
            $attribs['rows'] = (int) $this->rows;
        }
        if (empty($attribs['cols'])) {
            $attribs['cols'] = (int) $this->cols;
        }

        // build the element
        
        $id = 'tinymce'.$id;
        
        $xhtml = '<textarea name="' . $this->view->escape($name) . '"'
                . ' id="' . $this->view->escape($id) . '"'
                . $disabled
                . $this->_htmlAttribs($attribs) . '>'
                . $this->view->escape($value) . '</textarea>';
$xhtml .= '<script type="text/javascript">
Tg.TinyMce.register("'.$this->view->escape($id) . '");
</script>';
                
                
        return $xhtml;
	}
}
