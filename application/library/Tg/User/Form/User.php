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

class Tg_User_Form_User extends Tg_Form
{
    public function __construct($options = null) 
    { 
    	parent::__construct ($options);
		
		$this->addElement('hidden', 'id', array (
			'decorators'=>array('ViewHelper'))
			);
		
		$this->addElement('text', 'firstname', array (
			'label' 	 => 'Firstname',
			'required' 	 => true,
			'filters' 	 => array('StringTrim'),
			'validators' => array(
				array('StringLength', false, array(3, 50))
			),
			'class'		 => 'text'
		));

		$this->addElement('text', 'lastname', array (
			'label' 		=> 'Last name',
			//'required' 		=> true,
			'filters' 	 => array('StringTrim'),
			'validators' => array(
				array('StringLength', false, array(3, 50))
			),
			'class'		 => 'text'
		));

		$this->addElement('text', 'email', array (
			'label' 		=> 'Email',
			'required' 		=> true,
			'filters' 	 => array('StringTrim'),
			'validators' => array(
				array('StringLength', false, array(3, 50))
			),
			'class'		 => 'text'
		));

		$this->addElement('text', 'changePassword', array (
			'label' 		=> 'Password ',
			'filters' 	 => array('StringTrim'),
			'validators' => array(
				array('StringLength', false, array(3, 50))
			),
			'class'		 => 'text'
		));

		$this->addElement('text', 'changePasswordConfirm', array (
			'label' 		=> 'Password confirm',
			'filters' 	 => array('StringTrim'),
			'validators' => array(
				array('StringLength', false, array(3, 50))
			),
			'class'		 => 'text'
		));

		$this->addElement('submit', 'submit', array (
			'label' => 'Update',
			'class' => 'submit'
		));
    }
}