<?php
class Tg_View_Helper_PathCore
{
	function pathCore ($file='')
	{
		return Tg_Site::getCorePath($file);
	}
}