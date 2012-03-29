<?php
class Tg_View_Helper_PathTheme
{
	function pathTheme ()
	{
		return Tg_Site::getThemePath();
	}
}