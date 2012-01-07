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
 * Class representing a document in the Document Management System
 */ 
class Tg_Documents_Folder extends Tg_Tree_Adapter_Db_Node
{
	protected  $_files;
	private $_folderTable 	= 'file_folder';
	private $_table 		= 'file_folder_file';
	
	/**
	 * Get the files stored under a document
	 * 
	 * @return array $array
	 */
	function getFiles () 
	{
		//TODO - change to somehow use Tg_File::getFiles - decouple from Db 
		
		$table = new Tg_File_Db_Table_File ();
		$select = $table->select(true)
			->joinInner($this->_table,'file.id='.$this->_table.'.file_id',null)
			->where ($this->_table.'.folder_id=?',$this->id);
		
		return $table->fetchAll($select);
	}
	

	/**
	 * Add file
	 * 
	 * @return array $array
	 */
	function addFile ($file) 
	{
		//TODO - change to somehow use Tg_File::getFiles - decouple from Db 
		
		$table = new Tg_Documents_Db_Table_Folder ();
		$table->insert (array(
			'file_id'=>$file->id
			,'folder_id'=>$this->id
			,'name'=>'Test'
			));
	}
	

	/**
	 * Remove file
	 * 
	 * @return array $array
	 */
	function removeFile ($file) 
	{
		$table = new Tg_Documents_Db_Table_Folder ();
		$table->delete ('file_id='.$file->id.' AND folder_id='.$this->id);
	}
	
	public function toObject ()
	{
		$pageNode = array (
		    	'title'=>$this->title
		    	,'id'=>$this->id
		    	,'name'=>$this->name
		    	);
		
		return $pageNode;
	}
	
	public function toJson ()
	{
		$children = array ();
//
		foreach ($this->_childNodes as $child)
		{
			array_push($children, $child->toJson());			
		}
		
//		dump($children); die;
		
		$return = '{id:"'.$this->id.'",title:"'.$this->title.'",name:"'.$this->name.'",children:['.implode(',', $children).']}';	
		
		//dump($return); die;	
		
		return $return;
	}
}

?>