<?php
class Tg_View_Helper_Date
{	
/*
* Returns a string from a date
* 
* @param  Zend_Date $date 
* @param  string $dateFormat = null - a Zend_Date date format eg Zend_Date::DATE_SHORT
* @param  string $timeFormat = null - true/false or a Zend_Date time format eg Zend_Date::TIME_SHORT
* @return string $date
*/
	
    public function date($date, $dateFormat = null, $timeFormat = null, $justTime = null)
    {
		try {
			if (!$date instanceof Zend_Date) {			
				// assume Zend_Date::ISO_8601
				$date = new Zend_Date($date, Zend_Date::ISO_8601);
			}
			
			if($date->isToday()) {
				$dateString = 'Today';
			}
			elseif($date->isYesterday()) {
				$dateString = 'Yesterday';
			}
			else {
				$dateFormat=$dateFormat?$dateFormat:Zend_Date::DATE_SHORT;
				$dateString = $date->get ($dateFormat); // should use system wide local - set in bootstrap
			}
			
			if ($timeFormat) {
				if ($timeFormat===true)
					$timeFormat = Zend_Date::TIME_SHORT;
				$dateString .= ' '.$date->get ($timeFormat); // should use system wide local - set in bootstrap
				
			}
			
			if ($justTime) {
				$dateString = $date->get (Zend_Date::TIME_SHORT);
			}
			
			return $dateString;
		} catch (Exception $ecp) {
			return $date;
		}
    }
}