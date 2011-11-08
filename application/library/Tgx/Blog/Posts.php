<?php
require_once 'Zend/Db/Table/Abstract.php';

class Tgx_Blog_Posts extends Tg_Db_Table
{
	protected $_name = 'blog_post';
	protected $_sequence = true;
	protected $_rowClass = 'Tgx_Blog_Posts_Row';
	protected $_primary = 'id';
	
    protected $_dependentTables = array('Tgx_Comment_Comments');
    
}
?>