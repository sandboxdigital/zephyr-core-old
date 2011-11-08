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
class Tg_Auth_Adapter_User implements Zend_Auth_Adapter_Interface
{
	private $_username = '';
	private $_password = '';
	private $_passwordRequired = true;
	private $_user;

    /**
     * Sets username and password for authentication
     *
     * @return void
     */
    public function init($data)
    {
		$this->_username = $data['email'];
		
		if (isset($data['password']))
			$this->_password = $data['password'];
			
		if (isset($data['passwordNotRequired']) && $data['passwordNotRequired']===true)
			$this->_passwordRequired = false;
	}

	public function getIdentity(){
		return $this->_user;
	}

    /**
     * Performs an authentication attempt
     *
     * @throws Zend_Auth_Adapter_Exception If authentication cannot be performed
     * @return Zend_Auth_Result
     */
    public function authenticate ()
    {
        // get the user info from the database (username is an email address)
        $this->_user = null;
        try
        {
            $this->_user = Tg_User::getUserByEmail($this->_username);
        }
        catch (Exception $e)
        {
//            return new Zend_Auth_Result(Zend_Auth_Result::FAILURE_IDENTITY_NOT_FOUND,
//                array('user_name'=>$this->_username), array('invalid username'));
        }
        
        if (empty($this->_user))
            return new Zend_Auth_Result(Zend_Auth_Result::FAILURE_IDENTITY_NOT_FOUND,
                array('user_name'=>$this->_username), array('invalid username'));

        if (!$this->_passwordRequired) {
			return new Zend_Auth_Result(Zend_Auth_Result::SUCCESS,
                        $this->_user['id'],
                        array('successful login'));
        }
        // make a hash 
		$signature = strtolower(md5($this->_password));

		// validate credentials
		if ($signature !== $this->_user['password']){
			// password is not valid...
			return new Zend_Auth_Result(Zend_Auth_Result::FAILURE_CREDENTIAL_INVALID,
					array('user_name'=>$this->_username), array('invalid credentials'));
		}

		return new Zend_Auth_Result(Zend_Auth_Result::SUCCESS,
                        $this->_user['id'],
                        array('successful login'));
    }
}







