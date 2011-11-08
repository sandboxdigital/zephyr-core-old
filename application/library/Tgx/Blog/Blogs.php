<?php
require_once 'Zend/Db/Table/Abstract.php';

class Tgx_Blog_Blogs extends Tg_Db_Table
{
	protected $_name = 'blog_blog';
	protected $_sequence = true;
	protected $_rowClass = 'Tgx_Blog_Blogs_Row';
	
    protected $_dependentTables = array('Tgx_Blog_Posts');
}
?>