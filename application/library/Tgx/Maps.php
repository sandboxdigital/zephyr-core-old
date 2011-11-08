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


class Tgx_Maps extends Tgx_Crud_Factory
{
	protected static $_instance = null;
	public $_config;

	public function __construct() {
		$config= Zend_Registry::getInstance()->get('siteconfig');
		if (isset($config['maps'])) {
			$this->_config = $config['maps'];
		}
		
		$this->_table = new Tgx_Maps_Db_Table_Maps();
	}

    /**
     * Get singleton instance
     *
     * @return Tgx_Maps
     */
	public static function instance($options=array()) 
	{
		if(self::$_instance === null) {
			self::$_instance = new self($options);
		}
		return self::$_instance;
	}
}