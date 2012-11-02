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
    protected static $_instance = null;

    public function __construct()
    {
        $this->enabled = Zeph_Config::config('logging.enabled');

        if(!$this->enabled)
            return;

        $this->logger = new Zend_Log();
        $writers = array ();
        $configWriters = Zeph_Config::config('logging.writer');
        $configWriters = $configWriters ? $configWriters : array();

        foreach ($configWriters as $writerName)
        {
            if ($writerName == 'firebug')
            {
                $writer = new Zend_Log_Writer_Firebug();
                $writer->setPriorityStyle(8, 'TRACE');
                $writer->setPriorityStyle(9, 'TABLE');
                $writers[] = $writer;
            }
            else if ($writerName == 'db')
            {
                $db = Zend_Registry::get('db');
                $table = 'log';
                $cols = array (
                    'info'=>'message'
                );
                $writer = new Zend_Log_Writer_Db($db, $table);
                $writers[] = $writer;
            }
        }

        foreach($writers as $writer)
            $this->logger->addWriter($writer);

        $this->logger->addPriority('TRACE', 8);
        $this->logger->addPriority('TABLE', 9);
    }

    /**
     * Get singleton instance
     *
     * @return  Tg_Log $instance
     */
    public static function getInstance() {
        if(self::$_instance === null) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }

	public static function log($message, $level = Zend_Log::DEBUG) 
	{
        self::getInstance()->_log($message, $level);
	}

    public function _log($message, $level = Zend_Log::DEBUG)
    {
        if(!$this->enabled)
            return;

        $this->logger->log ($message, $level);
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

		$header = "From: admin@britishtours.com";			
		@mail('thomas@sandboxdigital.com.au', $subject, $message, $header);
	}
}