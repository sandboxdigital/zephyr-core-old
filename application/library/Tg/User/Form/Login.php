<?php
 /** Tg Framework 
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

class Tg_User_Form_Login extends Tg_Form {	
	 public function __construct($options = null, $redirectUrl = '') {
		parent::__construct($options);
		 
		$this->setMethod('post');

		$this->addElement('hidden', 'redirectUrl', array (
			'value' 	 => $redirectUrl,
			'decorators'=>array('ViewHelper')
		));

		$this->addElement('text', 'email', array (
			'label' 	 => 'Username',
			'required' 	 => true,
			'class' 	 => 'text'
		));

		$this->addElement('password', 'password', array (
			'label' 	 => 'Password',
			'required' 	 => true,
			'class' 	 => 'text'
		));

		$this->addElement('checkbox', 'rememberMe', array (
			'label' 	=> 'Remember me',
			'checked' 	=> '0'
		));

		$this->addElement('submit', 'login', array (
			'label' => 'Login'
		));
	}
}
?>