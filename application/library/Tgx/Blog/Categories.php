<?php
require_once 'Zend/Db/Table/Abstract.php';

class Tgx_Blog_Categories extends Tg_Db_Table_Crud
{
	protected $_name = 'blog_category';
	protected $_sequence = true;
	protected $_rowClass = 'Tgx_Blog_Db_Category';
}
?>