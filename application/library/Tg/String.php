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
}