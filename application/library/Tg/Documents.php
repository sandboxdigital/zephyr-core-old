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
 * Document Management Factory Class
 */

class Tg_Documents
{
	protected static $_instance = null;
	protected $_tree;
	protected $_rootNode;

	public function __construct() 
	{
		$this->_tree = new Tg_Tree(new Tg_Tree_Adapter_Db('file_folder', 'Tg_Documents_Folder'));
	}

	/**
	 * Get singleton instance
	 *
	 * @return  Tg_Documents $instance
	 */
	public static function getInstance() {
		if(self::$_instance === null) {
			self::$_instance = new self();
		}
		return self::$_instance;
	}

	/**
	 * Get the root folder
	 *
	 * @return  Tg_Documents_Folder $folder
	 */
	public static function getRootFolder ()
	{
		$inst = self::getInstance();
		
		if (!$inst->_rootNode)
			$inst->_rootNode = $inst->_tree->getRootNode();
		
		//dump ($inst->_rootNode); die;
			
		return $inst->_rootNode;
	}

	/**
	 * Get a folder based on it's id
	 *
	 * @return  Tg_Documents_Folder $folder
	 */
	public static function getFolderById ($id)
	{
        $inst = self::getInstance();
		$root =  self::getRootFolder();
		
		if ($root->id==$id)
			return $root;
		
		return $inst->_getFolderById ($id, $root);
	}

	/**
	 * Add a new folder to the $parent folder
	 *
	 * @return  Tg_Documents_Folder $folder
	 */
	public static function addFolder ($parent, $data)
	{
		$inst = self::getInstance();
		
		$data = self::_sanitiseData ($data);
		
		return $inst->_tree->addNode ($parent, $data);
	}
	

	/**
	 * Update data for $folder
	 *
	 * @return  Tg_Documents_Folder $folder
	 */
	public static function updateFolder ($folder, $data)
	{
		$inst = self::getInstance();
		
		$data = self::_sanitiseData ($data);
				
		return $inst->_tree->updateNode ($folder, $data);
	}
	

	/**
	 * Deleter $folder
	 *
	 * @return  bool
	 */
	public static function deleteFolder ($folder)
	{
		$inst = self::getInstance();
		return $inst->_tree->deleteNode ($folder);
	}

	/**
	 * Recursive method used by getFolderById
	 *
	 * @return  Tg_Documents_Folder $folder
	 */
	protected function _getFolderById ($id, $node)
	{
		$childNodes = $node->getChildNodes ();
		if (array_key_exists($id, $childNodes)) {
			return $childNodes[$id];
		}else {
			foreach ($childNodes as $childNode) {
				$return = self::_getFolderById ($id, $childNode);
				if ($return != null)
					return $return;
			}
		}
		
		$false = false;
		
		return $false;
	}
	
	/**
	 * Method used to clean data passed to addFolder and updateFolder
	 *
	 * @return  array $data
	 */
	protected static function _sanitiseData ($data) {
		if (isset($data['name']))
			$data['name'] = Tg_Helpers_Url::sanitiseUrl($data['name']) ;
		elseif (isset($data['title']))
			$data['name'] = Tg_Helpers_Url::sanitiseUrl($data['title']) ;
		return $data;
	}
	
	/**
	 * Method used to sanitise strings passed to addFolder and updateFolder
	 *
	 * @return  string $string
	 */
	protected static function _sanitisePath ($path) {
		$path = str_replace(' ','_', strtolower(trim($path)));
		$path = ereg_replace("[^a-zA-Z0-9_-]", '', $path);
		return $path;
	}
}
?>
