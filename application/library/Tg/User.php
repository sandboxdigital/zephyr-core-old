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
 * Tg User
 */
class Tg_User
{
	protected static $_instance = null;
	protected $_roles;

	private function __construct()
	{

	}

	public static function getInstance()
	{
		if (null === self::$_instance) {
			self::$_instance = new self();
		}
		return self::$_instance;
	}
	
	
	/**
	 * Registers a user using data provided
	 * 
	 * @return Tg_User_Db_User
	 */
	
	public static function register ($data, $roles = null)
	{
		$originalPassword = ($data['password']);
		$data['password'] = md5 ($data['password']);
		
		$userTable = new Tg_User_Db_Table_Users();
		$user = $userTable->createRow($data);
		$user->save();
		
		// TODO - add default roles from config
		$data['password'] = $originalPassword;
		
		Tg_Auth::login($data);
        $user = Tg_Auth::getAuthenticatedUser();
        return $user;
	}

	
	/**
	 * Returns a user based on their ID
	 * 
	 * @return Tg_User_Db_User
	 */
	public static function getUserById ($id)
	{
		$users = new Tg_User_Db_Table_Users ();
		return $users->fetchRow($users->select()->where ('id=?',$id));

	}
	
	/**
	 * Finds a user based on their email address. Returns null if not found
	 * 
	 * @param $email
	 * @return Tg_User_Db_User
	 */
	public static function getUserByEmail ($email)
	{
		$users = new Tg_User_Db_Table_Users ();
		return $users->fetchRow($users->select()->where ('email=?',$email));
	}

	public static function getUsersWithRoles ()
	{
		$db = Zend_Registry::get('db');
			
		return $db->fetchAll ('select user.*, group_concat(role.aclId) as roleAcls, group_concat(role.id) as roleIds from user
left join user_role on user.id= user_role.user_id
left join role on user_role.role_id = role.id
group by user.id');
			
	}

	public static function newRole ()
	{
		$rolesTable = new Tg_User_Db_Table_Roles ();

		return $rolesTable->createRow();
	}

	public static function getRoles ()
	{
		$inst = self::getInstance();
			
		if (!isset ($inst->_roles)) {
			$inst->_roles = array ();
				
			$rolesTable = new Tg_User_Db_Table_Roles ();

			$roles = $rolesTable->fetchAll(null, 'parentRole');

			foreach ($roles as $role)
			{
				$inst->_roles[$role->id] = $role;
			}
		}		

		return $inst->_roles;
	}

	public static function getRole ($id)
	{
		$roles = self::getRoles();
			
		if (isset ($roles[$id]))
			return $roles[$id];
		else
			return false;
	}

	public static function getUsers ()
	{
		$userTable = new Tg_User_Db_Table_Users();			
		return $userTable->fetchAll ();
	}

	public static function getUserNames ()
	{
		$users = self::getUsers();
		$userNames = array ();
		foreach ($users as $user)
			$userNames[$user->id] = $user->firstname .' '.$user->lastname;
		return $userNames;
	}
}