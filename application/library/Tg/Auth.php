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
class Tg_Auth 
{
    protected static $_instance = null;
    protected $_storage = null;
    protected $_adapter = null;
    protected $_auth = null;
    protected $_authenticatedUser = null;

    private function __construct()  
    {
        // TODO get storage and adapter from config
    
		$this->_storage = new Tg_Auth_Storage_Session();
		$this->_adapter = new Tg_Auth_Adapter_User();
		$this->_auth = Zend_Auth::getInstance();
        $this->_auth->setStorage($this->_storage);
    }

    public static function getInstance()
    {
        if (null === self::$_instance) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }
	
	public static function login ($data)
	{
		if ((isset($data['rememberMe'])) && $data['rememberMe'] == '1')
		{
			Zend_Session::rememberMe() ; //default is 2 weeks
		}
		$return = self::authenticate ($data);
		
		if (!$return){
			Zend_Session::forgetMe() ;
		}
		return $return;
	}
	
	public static function forceLogin ($email)
	{
		$data = array (
			'email'=>$email,
			'passwordNotRequired'=>true
		);
		
		$inst = self::getInstance();
		$inst->_adapter->init ($data);
        
        $result = $inst->_auth->authenticate($inst->_adapter);
        
        $return = $result->isValid();
		
		if (!$return){
			Zend_Session::forgetMe() ;
		}
		return $return;
	}
	
	public static function logout ()
	{
		$inst = self::getInstance();
		Zend_Session::forgetMe();
		Zend_Session::destroy(true, false);
		$inst->_auth->clearIdentity();
	}
	
	public static function authenticate ($data)
	{
		// set adapter
		$inst = self::getInstance();
		$inst->_adapter->init ($data);
        
        $result = $inst->_auth->authenticate($inst->_adapter);
        
        if ($result->isValid()) {
            return true;
        }
		else {
            return false;
        }
	}
	
	public static function getIdentity ()
	{
		$inst = self::getInstance();
		return $inst->_auth->getIdentity();
	}
	
	/**
	 * Get's the currently authenitcated user
	 * 
	 * @return Tg_User_Db_User
	 */
	
	public static function getAuthenticatedUser ()
	{
		$inst = self::getInstance();
		
		if ($inst->_authenticatedUser == null) 
		{
			$user = $inst->_auth->getIdentity();
			if ($user)
				$inst->_authenticatedUser = Tg_User::getUserById($user['id']);
			else
				$inst->_authenticatedUser = false;
		}
			
		return $inst->_authenticatedUser;
	}
}