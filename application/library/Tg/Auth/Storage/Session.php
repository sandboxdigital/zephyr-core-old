<?php
/**
 * Dodo - To-do list application
 *
 * License
 *
 * Simply put:
 * You can use or modify this software for any personal or commercial
 * applications with the following exception:
 *   - You cannot host this software using the Dodo name or any
 *      images from the Dodo website including any logos.
 *
 * @author    Greg Wessels (greg@threadaffinity.com)
 *
 * www.threadaffinity.com
 */
class Tg_Auth_Storage_Session implements Zend_Auth_Storage_Interface
{
    protected $_session;
    private $_sessionName = 'tg_auth';
    private $_storageName = 'id';

    public function __construct($session = null)
    {
        $this->_session  = new Zend_Session_Namespace('identity');
    }

    /**
     * Defined by Zend_Auth_Storage_Interface
     *
     * @return boolean
     */
    public function isEmpty()
    {
        return !isset($this->_session->{$this->_sessionName});
    }

    /**
     * Defined by Zend_Auth_Storage_Interface
     *
     * @return mixed
     */
    public function read()
    {
        return array(
                $this->_storageName=>$this->_session->{$this->_sessionName});
    }

    /**
     * Defined by Zend_Auth_Storage_Interface
     *
     * @param  mixed $contents
     * @return void
     */
    public function write($contents)
    {
        //print_r($contents);
        
        $this->_session->{$this->_sessionName} = $contents;
    }

    /**
     * Defined by Zend_Auth_Storage_Interface
     *
     * @return void
     */
    public function clear()
    {
        unset($this->_session->{$this->_sessionName});
    }
}
