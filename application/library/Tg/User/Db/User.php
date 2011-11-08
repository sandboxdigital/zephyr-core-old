<?php
class Tg_User_Db_User extends Tg_Db_Table_Row
{
	protected $_roles;

	public function __get ($name)
	{
		if ($name == 'name')
		return $this->_data['firstname'].' '.$this->_data['lastname'];
		else
		return parent::__get ($name);
	}

	public function addRole ($roleId)
	{
		$roles = $this->getRoles ();
		
		// don't add if user is already a member
		foreach ($roles as $role) {
			if ($roleId == $role->id)
				return;
		}
		
		$db = Zend_Registry::get ('db');
		$db->insert ('user_role', array ('role_id'=>$roleId,'user_id'=>$this->id));
	}

	public function deleteRole ($role)
	{
		$db = Zend_Registry::get ('db');
		$db->delete ('user_role', 'role_id='.$role.' AND user_id='.$this->id);
	}

	public function getRoles ()
	{
		if (empty ($this->_roles)) {
			$db = Zend_Registry::get ('db');
			$select = $db->select ()
			->from('role', array ('id','aclId','name'))
			->joinInner('user_role','role.id=user_role.role_id', array ('user_id'))
			->where ('user_id=?',$this->id);
				
			$this->_roles = $db->fetchAll($select);
		}
		return $this->_roles;
	}

	public function getRolesAcls ()
	{
		$roleRows = $this->getRoles ();
		$roleAcls = array ();
		foreach ($roleRows as $row) {
			$roleAcls[] = $row->aclId;
		}

		return $roleAcls;
	}

	/**
	 * Returns true if user is a member of a role
	 *
	 * @param $roleOrRoles string|array
	 * @return boolean
	 */
	public function hasRole ($roleOrRoles)
	{
		return $this->isMemberOf($roleOrRoles);
	}

	public function isMemberOf ($roleOrRoles)
	{
		$roles = $this->getRolesAcls ();

		if (is_array($roleOrRoles)) {
			foreach ($roleOrRoles as $role) {
				if (in_array($role, $roles))
				return true;
			}
		} elseif (is_string($roleOrRoles)) {
			if (in_array($roleOrRoles, $roles))
			return true;
		}
		return false;
	}

	public function update ($data)
	{
		$this->setFromArray($data);
		$this->save();
	}

	public function resetPassword ()
	{
		$newPassword = $this->_generatePassword ();

		$this->password = md5($newPassword);
		$this->save ();
		
		$body = "Your password has been reset\n\nYour new password is: ".$newPassword;
		
		$mail = new Zend_Mail();
		$mail->setBodyText($body);
		$mail->setFrom('noreply@'.$_SERVER['SERVER_NAME'], 'Admin');
		$mail->addTo($this->email, $this->name);
		$mail->setSubject('Password reset');
		$mail->send();
	}

	public function setPassword ($newPassword)
	{

		$this->password = md5($newPassword);
		$this->save ();
	}
		
	private function _generatePassword ($length = 8)
	{

		// start with a blank password
		$password = "";

		// define possible characters
		$possible = "0123456789bcdfghjkmnpqrstvwxyzBCDFGHJKLMNPQRSTVWXZ";

		// set up a counter
		$i = 0;

		// add random characters to $password until $length is reached
		while ($i < $length) {

			// pick a random character from the possible ones
			$char = substr($possible, mt_rand(0, strlen($possible)-1), 1);

			// we don't want this character if it's already in the password
			if (!strstr($password, $char)) {
				$password .= $char;
				$i++;
			}

		}

		// done!
		return $password;

	}

}