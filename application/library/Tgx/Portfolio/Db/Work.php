<?php
/**
 * Tg Framework 
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://framework.zend.com/license/new-bsd
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@zend.com so we can send you a copy immediately.
 *
 * @copyright  Copyright (c) 2009 Thomas Garrood (http://www.garrood.com)
 * @license    New BSD License
 */


class Tgx_Portfolio_Db_Work extends Tgx_Db_Table_Row
{
	protected $_name = 'work';
	private $_blog;
	private $_block;

	public function getPrevious ()
	{
		$table = new Tgx_Portfolio_Db_Table_Works();
		return $table->fetchRow("`sort`<".$this->sort, 'sort DESC LIMIT 1');
	}

	public function getNext ()
	{
		$table = new Tgx_Portfolio_Db_Table_Works();
		return $table->fetchRow("`sort`>".$this->sort, 'sort ASC LIMIT 1');
	}
	
}
?>