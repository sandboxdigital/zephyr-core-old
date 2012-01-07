<?php
class Tg_View_Helper_Trim
{	
	function trim ($str, $n, $delim='...') { 
	   $len = strlen($str); 
	   if ($len > $n) { 
	       preg_match('/(.{' . $n . '}.*?)\b/', $str, $matches); 
	       return rtrim($matches[1]) . $delim; 
	   } 
	   else { 
	       return $str; 
	   } 
	}
} 
