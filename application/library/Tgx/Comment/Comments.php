<?php
require_once 'Zend/Db/Table/Abstract.php';

class Tgx_Comment_Comments extends Zend_Db_Table_Abstract
{
	protected $_name = 'comment';
	protected $_sequence = true;
	protected $_rowClass = 'Tgx_Comment_Comments_Row';
}
?>