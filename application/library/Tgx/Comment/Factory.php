<?php
/**
 * Comment Factory Class
 *
 * @copyright  Copyright (c) 2008 Delete Limited (http://www.deletelondon.com)
 */

class Tgx_Comment_Factory {
	protected static $_instance = null;
	static $_blogs;

	private function __construct() {
		$this->_blogs = array ();
	}

    /**
     * Get singleton instance
     *
     * @return Tgx_Comment_Factory
     */
	public static function getInstance() {
		if(self::$_instance === null) {
			self::$_instance = new self();
		}
		return self::$_instance;
	}

    /**
     * Gets refTables used
     *
     * @param  none
     * @return Zend_Db_Table_Rowset_Abstract
     */
	function getRefTables () {
		$db =  Zend_Registry::get('db');

		//$select = $this->db->select();
		//$select->from(array('bp' => 'blog_post'), array('count' => 'COUNT(DISTINCT(bp.id))'));
		$countQuery = $db->query("select distinct refTable from comment;");
		$countResults = $countQuery->fetchAll();
		return $countResults;
	}
    /**
     * Gets all comments
     *
     * @param  none
     * @return Zend_Db_Table_Rowset_Abstract
     */
	function getComments ($refTable=null, $refTableId=null, $published=true, $order='dateAdded ASC') {
		$comments = new Tgx_Comment_Comments();

		// adds commentCount column to rowset
		$select = $comments->select();
		$select->from ($comments);
		if ($refTable)
			$select->where ("refTable='".$refTable."'");
		if ($refTableId)
			$select->where ("refTableId=".$refTableId);
		if ($published)
			$select->where ("published='yes'");

		foreach(explode(',', $order) as $o) {
			$select->order($o);
		}

//		echo $select;
		
		return $comments->fetchAll($select);
	}

	function getNumberOfComments ($publishedOnly = false)
	{
		$comments = new Tgx_Comment_Comments();

		// adds commentCount column to rowset
		$select = $comments->select();
		$select->from ($comments, array('count' => 'COUNT(DISTINCT(id))'));
		if ($publishedOnly == true)
			$select->where ("published='yes'");
		else
			$select->where ("published!='yes'");

		$countRow = $comments->fetchRow($select);

		return $countRow->count;
	}

    /**
     * Gets a comments
     *
     * @param  int $id
     * @return Zend_Db_Table_Row_Abstract
     */
	function getComment ($id) {
		$comments = new Tgx_Comment_Comments();
		if ($id)
			return $comments->fetchRow("id=$id");
		else
			return $comments->createRow();
	}

    /**
     * Adds a comment
     *
     * @param  array $values
     */
	function addComment ($values, $authorRequired=true, $published=false) {
		$user = Tg_Auth::getAuthenticatedUser();
		if ($user) {
			$authorId = $user->id;
		} else if ($authorRequired) {
			if (!isset($values['email']) || !isset($values['name']))
				throw new Zend_Exception("Name and email required");

			$user = Tgx_User::getInstance()->getUserByEmail($values['email']);
			if ($user)
				$authorId = $user->id;
			else
				$authorId = Tgx_User::getInstance()->createBasicUser($values);
		} else
			$authorId = 1; // default users

		$commentTable = new Tgx_Comment_Comments();
		$comment = $commentTable->createRow();
		$comment->dateAdded = new Zend_Db_Expr('NOW()');
		$comment->published = ($published)?'yes':'no';
		$comment->comment = $values['comment'];
		$comment->refTable = $values['refTable'];
		$comment->refTableId = $values['refTableId'];
		$comment->authorId = $authorId;
		return $comment->save();
	}

    /**
     * Saves a comment
     *
     * If the post doesn't have an ID it inserts a new comment
     *
     * @param  Zend_Db_Table_Row_Abstract $comment
     * @param  Zend_Form $form
     */
	function saveComment ($comment, $values) {
        $values['authorId']=1;
		unset($values['submit']);
		$comment->setFromArray($values);
		$comment->save();
	}
}
?>
