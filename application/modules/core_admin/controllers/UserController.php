<?php

class Core_Admin_UserController extends Tg_Site_Controller
{

	public function init ()
	{
		parent::init ();
		
		$this->view->headScript()->appendFile('/core/js/jquery.confirm.js');	
		$this->view->headLink()->appendStylesheet('/core/css/pm.css');
	}	
	
    public function indexAction() 
    {
		$this->_forward ('users');
    }

	public function usersAction() 
	{
		$this->view->users = Tg_User::getUsersWithRoles ();
		$this->view->roles = Tg_User::getRoles();
	}

    public function userEditAction ()
    {    	
    	$user = Tg_User::getUserById($this->_getParam ('id'));
			
    	$form = new Tg_User_Form_User ();
    	$form->setAction('/admin/user/user-edit');
    	
    	if ($this->_request->isPost()) {
	        if ($form->isValid($_POST)) {
	        	$data =$form->getValues ();
				$user->update ($data);
				
				if (!empty($data['changePassword']) && $data['changePassword'] == $data['changePasswordConfirm'])
				{
					$user->setPassword ($data['changePassword']);
				}
				
				$this->view->message = "Updated";
			}
    	} else
    		$form->setDefaults($user->toArray());
    		
		$this->view->user = $user;
		$this->view->userRoles = $user->getRoles();
		$this->view->roles = Tg_User::getRoles();
		$this->view->form = $form;
		$this->view->title = 'Edit user';
		$this->render('user-form');
    }

    public function userDeleteAction ()
    {
    	$user = Tg_User::getUserById($this->_getParam ('id'));
		if (!$user)
			throw new Exception ("User not found");
			
		if ($user == $this->_currentUser)
			throw new Exception ("You can't delete yourself");
		
    	$user->delete();
    	
		echo 'User deleted';
		die;
    }

    public function userRolesAction ()
    {    	
    	$user = Tg_User::getUserById($this->_getParam ('user'));
    		
    	if ($this->_getParam('do') == 'ADD') {
    		$user->addRole ($this->_getParam ('role'));
    	} else if ($this->_getParam('do') == 'DELETE') {
    		$user->deleteRole ($this->_getParam ('role'));
    	} 
    	
    	
		$this->view->user = $user;
		$this->view->userRoles = $user->getRoles();
		$this->view->roles = Tg_User::getRoles();
    }

	public function rolesAction() 
	{
		$this->view->roles = Tg_User::getRoles();
	}

    public function roleEditAction ()
    {    	
    	$form = new Tg_User_Form_Role ();
    	$form->setAction('/admin/user/role-edit');
    	
    	$role = Tg_User::getRole($this->_getParam ('id'));
    	if (!$role) {
    		$role = Tg_User::newRole();
    		$form->removeElement ('id');
    	}
    	
    	if ($this->_request->isPost()) {
	        if ($form->isValid($_POST)) {
				$role->setFromArray ($form->getValues ());
				$role->save ();
				$this->view->message = "Updated";
			}
    	} else
    		$form->setDefaults($role->toArray());
    		
		$this->view->role = $role;
		$this->view->form = $form;
		$this->view->title = 'Edit role';
		$this->render('role-form');
    }

    public function roleDeleteAction ()
    {
		
    	$role = Tg_User::getRole($this->_getParam ('id'));
    	
    	$role->delete();
    	
		echo 'Deleted';
		die;
    }
}