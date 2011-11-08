<?php
class Tg_View_Helper_FormDateMonthYear extends Zend_View_Helper_FormText
{
    
    private $_format = 'yyyy-MM-dd';
    
	public function formDateMonthYear($name, $value = null, $attribs = null)
	{
		$info = $this->_getInfo($name, $value, $attribs);
		extract($info); // name, value, attribs, options, listsep, disable

		$monthVal = null;
		$yearVal = null;
		
    	if (isset($attribs['format']))
    		$this->_format = $attribs['format'];
		
	   	$date = new Zend_Date($value, $this->_format);
//		if (isset($value) && $value instanceof Zend_Date) {
			$monthVal = $date->get('MM');
			$yearVal = $date->get('yyyy');
//		}
		
		$monthsOptions = array('1' => '01', '2' => '02', '3' => '03', '4' => '04', '5' => '05', '6' => '06',
                    '7' => '07', '8' => '08', '9' => '09', '10' => '10', '11' => '11', '12' => '12');

		$yearOptions = array();
        $thisYear = date('Y');
        for ($i = 0; $i < 15; ++$i) {
            $val = $thisYear + $i;
            $yearOptions[$val] = $val;
        }
        
		$month = $this->view->formSelect ($name.'[month]',$monthVal,null,$monthsOptions);
		$year = $this->view->formSelect ($name.'[year]',$yearVal,null,$yearOptions);
 

		$xhtml = $month .' / '.$year;
		return $xhtml;
	}
}
