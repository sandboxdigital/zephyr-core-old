<?php
/**
 * Tg Site Page Database Gateway Class 
 *
 * @copyright  Copyright (c) 2009 Thomas Garrood (http://www.garrood.com)
 *
 */

require_once 'Zend/Db/Table/Abstract.php';

class Tg_Nav_Db_Navitems extends Tg_Db_Table
{
	protected $_name = 'nav_item';
	protected $_sequence = true;
	protected $_rowClass = 'Tg_Nav_Db_Navitem';
}
?>