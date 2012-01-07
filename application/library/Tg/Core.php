<?php
/**
 * Tg Framework 
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://framework.zend.com/license/new-bsd
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@zend.com so we can send you a copy immediately.
 *
 * @copyright  Copyright (c) 2009 Thomas Garrood (http://www.garrood.com)
 * @license    New BSD License
 */


/**
 * Tg Core functions
 */

class Tg_Core
{
	
	
}


// simple data dump for debugging
function dump($var, $label = '', $type='info') {
	echo '<pre style="border:1px solid black; color:#000000;font-family:Courier;background-color:#BDE3CC; padding:5px; margin:5px; font-size:11px;">';
	if($label) echo '<h3 style="margin-top:0;">'.$label.'</h3>';
	print_r($var);
	echo '</pre>';
}


function trace ($s)
{
	dump($s);	
}


// simple data dump for debugging
function stacktrace() {
	echo '<pre style="border:1px solid black; color:#000000;font-family:Courier;background-color:#BDE3CC; padding:5px; margin:5px; font-size:11px;">';
	echo '<h3 style="margin-top:0;">Stacktrace</h3>';
	debug_print_backtrace();
	echo '</pre>';
}

function debug ($m) {
	if(Zend_Registry::isRegistered('logger'))
	{
		$logger = Zend_Registry::get('logger');
		$logger->info ($m);		
	}
}