<?php
class Tg_View_Helper_FormJqueryDate extends Zend_View_Helper_FormText
{
	public function formJqueryDate($name, $value = null, $attribs = null)
	{
		$info = $this->_getInfo($name, $value, $attribs);
		extract($info); // name, value, attribs, options, listsep, disable

		// build the element
		$disabled = '';
		if ($disable) {
			// disabled
			$disabled = ' disabled="disabled"';
		}

		if (isset($attribs['format'])){
			$format = $attribs['format'];
			unset($attribs['format']);
		} else
			$format = 'dd-MM-YYYY';
			
		$jFormat = str_replace('D','d',$format);
		$jFormat = str_replace('YYYY','yy', $jFormat);
		$jFormat = str_replace('MMM','M', $jFormat);

		// convert Zend_Date to JqueryDate
		if (isset($value) && $value instanceof Zend_Date) {
			$value = $value->toString ($format);
		} else
			$value = '';

		// XHTML or HTML end tag?
		$endTag = ' />';
		if (($this->view instanceof Zend_View_Abstract) && !$this->view->doctype()->isXhtml()) {
			$endTag= '>';
		}

		$xhtml = '<input type="text"'
			. ' name="' . $this->view->escape($name) . '"'
			. ' id="' . $this->view->escape($id) . '"'
			. ' value="' . $this->view->escape($value) . '"'
			. $disabled
			. $this->_htmlAttribs($attribs)
			. $endTag;
			
		$jsOptions = array (
			'showAnim'=> "'fadeIn'" 
			,'dateFormat'=>"'$jFormat'"
	    	,'changeMonth'=>true
	    	,'changeYear'=>true
	    	,'showButtonPanel'=>true
	    	);
	    	
		if (isset($attribs['jsOptions']))
			$jsOptions = array_merge($jsOptions,$attribs['jsOptions']);
			
		$xhtml .= '
<script type="text/javascript">
$(document).ready(function() {
	$("#'.$this->view->escape($id).'").datepicker ({
		myVar:"awesome"';
		foreach ($jsOptions as $key => $value)
		{
			$xhtml .= ','.$key.':'.$value;
		}
		
    $xhtml .= '});
});
</script>' ;

		return $xhtml;
	}
}
