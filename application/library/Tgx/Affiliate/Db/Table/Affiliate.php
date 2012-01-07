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

class Tgx_Affiliate_Db_Table_Affiliate extends Tg_Db_Table_Crud
{
	protected $_name = 'affiliate'; 
	
	public function getAll() 
	{
		$select = $this->select()
		->order ('firstname');

		return $this->fetchAll($select);
	}
	
	public function getItem ($id) 
	{
		return $this->fetchRow ("id=".$id);
	}

	public function getNew ()
	{
		return $this->fetchNew ();
	}
	
	public function deleteItem ($id) 
	{
		return $this->delete ("id=".$id);
	}
}