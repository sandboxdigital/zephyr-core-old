<?php
require_once 'Zend/Db/Table/Row/Abstract.php';

class Tgx_Comment_Comments_Row extends Zend_Db_Table_Row_Abstract
{
	protected $_name = 'comment';

    /**
     * Getter
     *
     * @return $var
     */
	public function __get ($name) {
		switch ($name) {
			case 'Author':
				return $this->getAuthor();
				break;
			default:
				return parent::__get($name);			
		}	
	}
	
	/**
	 * Gets the comments author
	 *
	 * @return $user
	 */
	function getAuthor () {
		return Tgx_User::getInstance()->getUserById ($this->authorId, true);
	}
}
?>