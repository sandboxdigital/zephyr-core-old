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
 * Tg Site
 */

class Tg_Site_Acl
{
	protected static $_instance = null;
	protected $_acl;
	protected $_pageRoles;

	/**
	 * Singleton pattern implementation makes "new" unavailable
	 *
	 */
	private function __construct()
	{
		$this->_acl = new Zend_Acl();

		// allow all resources by default
		$this->_acl->setRule(Zend_Acl::OP_REMOVE, Zend_Acl::TYPE_DENY);
		$this->_acl->setRule(Zend_Acl::OP_ADD, Zend_Acl::TYPE_ALLOW);

		$this->_getRolesFromDb();
		$this->_loadPageRoles();
		//
		//		// add our pages as resources.
		$root = Tg_Site::getInstance()->getRootPage();
		$this->_addPage($root);

	}

	/**
	 * Returns an instance of Tg_Site_Acl
	 *
	 */
	public static function getInstance()
	{
		if (null === self::$_instance) {
			self::$_instance = new self();
		}
		return self::$_instance;
	}

	private function _getRolesFromDb ()
	{
		$roles = Tg_User::getRoles ();

		foreach ($roles as $role)
		{
//			if (isset($role->parentRole))
//				$this->_acl->addRole(new Zend_Acl_Role($role->aclId), array($roles[$role->parentRole]->aclId));
//			else
				$this->_acl->addRole(new Zend_Acl_Role($role->aclId));
		}
	}

	private function _loadPageRoles () {
		$pages = new Tg_Site_Db_PageRoles ();
		$rows = $pages->fetchAll(null,'pageId');

		$this->_pageRoles = array ();

		foreach ($rows as $row)
		{
			if (!isset($this->_pageRoles[$row->pageId]))
				$this->_pageRoles[$row->pageId] = array ();
			
				if (isset($row->privilege))
					$priv = $row->privilege;
				else
					$priv = 'read';
			
			$privsArray = explode (',',$priv);
			foreach ($privsArray as $priv) {
				$this->_pageRoles[$row->pageId][] = array (
					'roleId'=>$row->roleId,
					'privilege'=>$priv
				);
			}
		}
	}


	private function _addPage ($page, $parentPath = null)
	{
		$this->_acl->addResource($page->path, $parentPath);

		if (isset($this->_pageRoles[$page->id])) {
			// we have roles assigned so DENY access to all roles by default
			$this->_acl->setRule(Zend_Acl::OP_ADD, Zend_Acl::TYPE_DENY, null, $page->path);
		
			// now add roles with access
			foreach ($this->_pageRoles[$page->id] as $role) {
				// grant access to role
				$this->_acl->allow(Tg_User::getRole($role['roleId'])->aclId, $page->path, $role['privilege']);

			}
		}

		foreach ($page->pages as $childPage)
		{
			$this->_addPage($childPage, $page->path);
		}
	}

	public static function addRoleToPage ($pageId, $roleId)
	{
		$inst = self::getInstance();
		
		$data = array ('pageId'=>$pageId, 'roleId'=>$roleId);
		
		$pages = new Tg_Site_Db_PageRoles ();
		if (!$pages->exists($data))		
			$pages->insert ($data);

		$inst->_loadPageRoles ();
	}

	public static function deleteRoleFromPage ($pageId, $roleId)
	{
		$inst = self::getInstance();
			
		$pages = new Tg_Site_Db_PageRoles ();
		$pages->delete ('pageId='.$pageId.' AND roleId='.$roleId);

		$inst->_loadPageRoles ();
			
	}

	public static function getPageRoles ($pageId)
	{
		$inst = self::getInstance();
			
		if (isset ($inst->_pageRoles[$pageId]))
			return $inst->_pageRoles[$pageId];
		else
			return array ();
	}

	/**
	 * Determines if a user has access to a page
	 *
	 * @param $user
	 * @param $resource
	 * @param $privilege
	 * @return unknown_type
	 */
	public static function isUserAllowed($user, $resource, $privilege = 'read')
	{

		$roles = array ('GUEST');

		// get our authenticated user
        if ($user)
        	$roles = $user->getRolesAcls ();
        if (count($roles) == 0)
			$roles = array ('GUEST');
		
		return self::isAllowed($roles, $resource, $privilege);
	}

	/**
	 *
	 * @param $role
	 * @param $resource
	 * @param $privilege
	 * @return unknown_type
	 */
	public static function isAllowed($roles, $resource, $privilege = 'read')
	{
		$inst = self::getInstance();
		
		$site = Tg_Site::getInstance();
		
		if ((isset($site->_config['disableACL'])) && $site->_config['disableACL']==true)
			return true;
		
		if (!$inst->_acl->has ($resource)) {
			$resource = null;
		}

		foreach ($roles as $role)  {
			if ($role == 'GUEST')
				$role = null;
			
			if ($inst->_acl->isAllowed($role, $resource, $privilege)) 
				return true;
		}
		return false;
	}

	public static function debugAcl ()
	{
		$ins = self::getInstance();

		dump ($ins->_acl);
	}
}