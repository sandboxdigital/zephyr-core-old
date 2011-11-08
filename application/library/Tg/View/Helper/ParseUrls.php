<?php
class Tg_View_Helper_ParseUrls
{	
	function parseUrls ($text) { 
	   $text = preg_replace(
			"/(?:^|\b)((((http|https|ftp):\/\/)|(www\.))([\w\.]+)([,:%#&\/?=\w+\.-]+))(?:\b|$)/is",
			"<a href=\"$1\" target=\"_blank\">$1</a>",
			$text);
		return $text;
	}
} 
