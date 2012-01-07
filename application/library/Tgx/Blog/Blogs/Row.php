<?php
require_once 'Zend/Db/Table/Row/Abstract.php';

class Tgx_Blog_Blogs_Row extends Tg_Db_Table_Row
{
	protected $_name = 'blog_blog';

    /**
     * Get all the posts for this blog
     *
     * @return Zend_Db_Table_Rowset_Abstract
     */
	public function getPosts ($limit = null) {
		$posts = new Tgx_Blog_Posts();

		// adds commentCount column to rowset
		$select = $posts->select()->setIntegrityCheck(false);
		$select->from ($posts);
		$select->joinLeft('comment', 'comment.refTableId=blog_post.id AND refTable=\'blog\'', array('COUNT(comment.id) as commentCount'));
		$select->group ('blog_post.id');
		$select->where ("published='yes'");
		$select->where ("blogId=".$this->id);
		$select->order ('datePublished DESC');

		if(isset($limit)) {
			$select->limit($limit);
		}

		$postsArray = $posts->fetchAll($select);

		// TODO - seems very dirty must be a cleaner way of doing this
		//for ($i=0;$i<count($postsArray);$i++) {
		foreach ($postsArray as $row) {
			$row->blog = $this;
		}

		return $postsArray;
	}
}
?>