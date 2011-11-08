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
 * Tg Site Layout Database Gateway Class 
 */


require_once 'Zend/Db/Table/Abstract.php';

class Tg_Site_Db_Layouts extends Tg_Db_Table
{
	protected $_name = 'site_layout';
	protected $_sequence = true;
	protected $_rowClass = 'Tg_Site_Db_Layout';
}
?>