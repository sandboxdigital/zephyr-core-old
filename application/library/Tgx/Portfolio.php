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

/**
 * Portfolio Factory Class
 */


class Tgx_Portfolio {

	public $_config;

	public function __construct() {
		$config= Zend_Registry::getInstance()->get('siteconfig');
		if (isset($config['work'])) {
			$this->_config = $config['work'];
		}
	}

	public function getAll($published = false) {
		$table = new Tgx_Portfolio_Db_Table_Works();

		$select = $table->select();
		$select->order('sort');

		if($published == true) {
			$select->where('published=?', 'Yes');
		}

		return $table->fetchAll($select);
	}

	public function getPublished($published = false) {

		return $this->getAll(true);
	}

	public function getItem($id) {
		$table = new Tgx_Portfolio_Db_Table_Works();

		return $table->fetchRow ("id=".$id);
	}

	public function getItemFromSlug($slug) {
		$db =  Zend_Registry::get('db');

		$select = $db->select();
		$select->from('work');
		$select->limit(1);

		$select->where('slug=?', $slug);

		$query = $db->query($select);
		$results = $query->fetchAll();

		if(isset($results[0])) {
			return $this->organiseInfo($results[0]);
		}
		else {
			return false;
		}
	}

	public function getLatestItem() {
		$db =  Zend_Registry::get('db');

		$select = $db->select();
		$select->from('work');
		$select->limit(1);
		$select->order('sort ASC');

		$query = $db->query($select);
		$results = $query->fetchAll();

		if(isset($results[0])) {
			return $this->organiseInfo($results[0]);
		}
		else {
			return false;
		}
	}
}