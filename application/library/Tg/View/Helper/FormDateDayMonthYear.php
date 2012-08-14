<?php
class Tg_View_Helper_FormDateDayMonthYear extends Zend_View_Helper_FormText
{
    
    private $_format = 'yyyy-MM-dd';
    
	public function formDateDayMonthYear($name, $value = null, $attribs = null)
	{
		$info = $this->_getInfo($name, $value, $attribs);
		extract($info); // name, value, attribs, options, listsep, disable

		$dayVal = null;
		$monthVal = null;
		$yearVal = null;

    	if (isset($attribs['format']))
    		$this->_format = $attribs['format'];

        if (false){
            $date = new Zend_Date($value, $this->_format);

            $dayVal = $date->get('dd');
            $monthVal = $date->get('MM');
            $yearVal = $date->get('yyyy');

            $monthsOptions = array('1' => '01', '2' => '02', '3' => '03', '4' => '04', '5' => '05', '6' => '06',
                '7' => '07', '8' => '08', '9' => '09', '10' => '10', '11' => '11', '12' => '12');

            $yearOptions = array();
            $thisYear = date('Y');
            for ($i = 0; $i < 15; ++$i) {
                $val = $thisYear + $i;
                $yearOptions[$val] = $val;
            }
            $day = $this->view->formSelect ($name.'[day]',$monthVal,null,$monthsOptions);
            $month = $this->view->formSelect ($name.'[month]',$monthVal,null,$monthsOptions);
            $year = $this->view->formSelect ($name.'[year]',$yearVal,null,$yearOptions);
        } else {
            if (!empty($value)){
                $date = new Zend_Date($value, $this->_format);

                $dayVal = $date->get('dd');
                $monthVal = $date->get('MM');
                $yearVal = $date->get('yyyy');
            }
            $day = $this->view->formText ($name.'[day]',$dayVal,array('maxlength'=>2));
            $month = $this->view->formText ($name.'[month]',$monthVal,array('maxlength'=>2));
            $year = $this->view->formText ($name.'[year]',$yearVal,array('maxlength'=>4));
        }

		$xhtml = '<div class="tgFormElementDateDayMonthYearContainer">'.$day .' / '.$month .' / '.$year.'</div>';
		return $xhtml;
	}
}
