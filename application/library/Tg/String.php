<?php

class Tg_String  {
	
	private $_string = '';
	
	function __construct($str)
	{
		$this->_string = $str;
	}	
	
	function startsWith($needle,$case=true) {
	    if($case){return (strcmp(substr($this->_string, 0, strlen($needle)),$needle)===0);}
	    return (strcasecmp(substr($this->_string, 0, strlen($needle)),$needle)===0);
	}
	
	function endsWith($needle,$case=true) {
	    if($case){return (strcmp(substr($this->_string, strlen($this->_string) - strlen($needle)),$needle)===0);}
	    return (strcasecmp(substr($this->_string, strlen($this->_string) - strlen($needle)),$needle)===0);
	}

	public static function sanitizeUrl($z)
	{
		$z = strtolower($z);
	    $z = preg_replace('/[^a-z0-9 -]+/', '', $z);
	    $z = str_replace(' ', '-', $z);
	    return trim($z, '-');
	}

    public static function hex2RGB($hexStr, $returnAsString = false, $seperator = ',') {
        $hexStr = preg_replace("/[^0-9A-Fa-f]/", '', $hexStr); // Gets a proper hex string
        $rgbArray = array();
        if (strlen($hexStr) == 6) { //If a proper hex code, convert using bitwise operation. No overhead... faster
            $colorVal = hexdec($hexStr);
            $rgbArray['red'] = 0xFF & ($colorVal >> 0x10);
            $rgbArray['green'] = 0xFF & ($colorVal >> 0x8);
            $rgbArray['blue'] = 0xFF & $colorVal;
        } elseif (strlen($hexStr) == 3) { //if shorthand notation, need some string manipulations
            $rgbArray['red'] = hexdec(str_repeat(substr($hexStr, 0, 1), 2));
            $rgbArray['green'] = hexdec(str_repeat(substr($hexStr, 1, 1), 2));
            $rgbArray['blue'] = hexdec(str_repeat(substr($hexStr, 2, 1), 2));
        } else {
            return false; //Invalid hex color code
        }
        return $returnAsString ? implode($seperator, $rgbArray) : $rgbArray; // returns the rgb string or the associative array
    }
}