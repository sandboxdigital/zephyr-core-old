<?php

/**
 * Description of Tg_Helpers_Url
 *
 * @author Thomas
 */
class Tg_Helpers_Url {


	public static function sanitiseUrl ($path, $toLower = true) {
		$path = trim($path);
		if ($toLower)
			$path = strtolower($path);
		$path = str_replace(' ','-', $path);
		$path = preg_replace("[^a-zA-Z0-9_-]", '', $path);
		return $path;
	}
	
	public function getLastDir ($uri)
	{
		$uriS = new Tg_String ($uri);

		if (strpos($uri,'?'))
			$uri = substr($uri, 0, strpos($uri,'?'));

		if ($uriS->endsWith('/'))
			$uri = substr($uri, 0, strlen($uri)-1);

		$paths = explode('/',$uri);

		$slug = $paths[count($paths)-1];
		
		return $slug;		
	}
}