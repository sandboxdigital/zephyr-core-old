<?php
/**
 */

require_once 'Zend/Db/Table.php';

class Tg_Tree_Adapter_Db extends Tg_Tree_Adapter_Abstract
{
	protected $_table;
	protected $_tableClass;
	protected $_nodeClass;
	protected $_rootNode;
	
	public function __construct ($table, $nodeClass = 'Tg_Tree_Adapter_Db_Node')
	{
		$this->_table = $table;
		$this->_tableClass = new Zend_Db_Table ($this->_table);
		$this->_nodeClass = $nodeClass;
	}
	
	public function getRootNode ()
	{
		$select = $this->_tableClass->select();
		$select->order ('left');
		
		$rows = $this->_tableClass->fetchAll($select);
		if (!$rows ) {
			throw new Zend_Exception ('Tg_Tree: No Root Node');		
		}
		
		$parent = null;
		$currentNode = $this->_rootNode = new $this->_nodeClass ($rows->current(), $parent);
		$rows->next ();
		$this->_populateNodes($currentNode, $rows);
		
		return $this->_rootNode;
	}
	
	function addNode ($parent, array $data)
	{		
		if (!$parent instanceof Tg_Tree_Adapter_Db_Node)
			throw new Exception ('Parent must be a Tg_Tree_Adapter_Db_Node');	
			
		$data['left'] = $parent->right;
		$data['right'] = $parent->right+1;
		unset($data['id']);
		
		// shift left and right values by 2
		$left = $this->_tableClass->getAdapter()->quoteIdentifier('left');
		$right = $this->_tableClass->getAdapter()->quoteIdentifier('right');
		
		$this->_tableClass->update(array('right'=>new Zend_Db_Expr("$right+2")),"$right>=".$parent->right);
		$this->_tableClass->update(array('left'=>new Zend_Db_Expr("$left+2")),"$left>=".$parent->right);
		
		// insert node
		$currentRow = $this->_tableClass->createRow($data);
		$currentRow->save();
		$newNode = new $this->_nodeClass ($currentRow, $parent);
		$parent->populateChildNode($newNode);

		return $newNode;
	}
	
	function updateNode ($node, array $data)
	{		
		$this->_tableClass->update ($data,"id=".$node->id);
	}
	
	function deleteNode ($node) {
		if (!$node instanceof Tg_Tree_Adapter_Db_Node)
			throw new Exception ('Node must be a  Tg_Tree_Adapter_Db_Node');	
		
		if ($node->left==1)
			throw new Exception ('Can\'t delete ROOT page');	
		
		$left = $this->_tableClass->getAdapter()->quoteIdentifier('left');
		$right = $this->_tableClass->getAdapter()->quoteIdentifier('right');
		
		$dif = ($node->right-$node->left)+1;
		
		// delete page and all subpages
		$where = array(
			"$left>=".$node->left,
			"$right<=".$node->right);
		
		$this->_tableClass->delete ($where);
		
		// update tree
		$this->_tableClass->update (array ("left"=>new Zend_Db_Expr("$left-$dif")), "$left>{$node->left}");
		$this->_tableClass->update (array ("right"=>new Zend_Db_Expr("$right-$dif")), "$right>{$node->right}");		
	}
	
	function moveNode ($parent, $child, $previousSibling)
	{		
		if (!$parent instanceof Tg_Tree_Adapter_Db_Node)
			throw new Exception ('Parent must be a Tg_Tree_Adapter_Db_Node');	
			
		$data['left'] = $parent->right;
		$data['right'] = $parent->right+1;
		unset($data['id']);
		
		// shift left and right values by 2
		$left = $this->_tableClass->getAdapter()->quoteIdentifier('left');
		$right = $this->_tableClass->getAdapter()->quoteIdentifier('right');
		
		$this->_tableClass->update(array('right'=>new Zend_Db_Expr("$right+2")),"$right>=".$parent->right);
		$this->_tableClass->update(array('left'=>new Zend_Db_Expr("$left+2")),"$left>=".$parent->right);
		
		// insert node
		$currentRow = $this->_tableClass->createRow($data);
		$currentRow->save();
		$newNode = new $this->_nodeClass ($currentRow->toArray(), $parent);
		$parent->childNodes[$newNode->id] = $newNode;

		return $newNode;
	}
	
	private function _populateNodes ($currentNode, $rows)
	{
		$currentRow=$rows->current();
		while ($currentRow!=null && $currentRow->left < $currentNode->right) {
			$newNode = new $this->_nodeClass ($currentRow, $currentNode);
			$rows->next ();
			$this->_populateNodes($newNode, $rows);
			$currentNode->populateChildNode ($newNode);
			$currentRow=$rows->current();
//			dump ($currentRow);
		}		
	}
}
?>