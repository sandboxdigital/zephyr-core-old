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
 * Tg Logging Class 
 */

class Tg_Log
{

	public static function log($message, $level = Zend_Log::DEBUG) 
	{
    	$logger = Zend_Registry::get('logger');
    	if ($logger)
    		$logger->log ($message, $level);
	}
	
	
	public static function logToDb($message, $level = Zend_Log::DEBUG) 
	{	
		try {
	    	$data = array(
	    		'info'=>$message
	    		
	    	);
	    	
	    	$db = Zend_Registry::get('db');    	
	    	$db->insert ('log', $data);
		} catch (Exception $e) {
		
		}
	}
	
	public static function logToEmail($message, $subject='') 
	{
		if (empty($subject))
			$subject=$_SERVER['SERVER_NAME'].' LOG TO EMAIL';
		
//		echo '<pre>';
//		echo $message;
//		echo '</pre>';
//		die;			
		$header = "From: admin@britishtours.com";			
		@mail('thomas@sandboxdigital.com.au', $subject, $message, $header);
	}
}