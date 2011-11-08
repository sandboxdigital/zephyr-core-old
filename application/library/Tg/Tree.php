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
 * Tree Class
 */


class Tg_Tree
{
	protected $_adapter;
	
	public function __construct ($adapter) 
	{
//		$config= Zend_Registry::getInstance()->get('siteconfig');
//		if (isset($config['maps'])) {
//			$this->_config = $config['maps'];
//		}
		
		$this->_adapter = $adapter;
	}
	
	public function getRootNode ()
	{
		return $this->_adapter->getRootNode();
	}
	
	public function addNode ($parent, $data)
	{
		return $this->_adapter->addNode($parent, $data);
	}
	
	function updateNode ($node, array $data)
	{		
		$this->_adapter->updateNode ($node, $data);
	}
	
	public function deleteNode ($node)
	{
		return $this->_adapter->deleteNode ($node);
	}
}