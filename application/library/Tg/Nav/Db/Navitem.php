<?php
/**
 * Tg Nav Navitem Database Gateway Class
 *
 * @copyright  Copyright (c) 2009 Thomas Garrood (http://www.garrood.com)
 *
 */

class Tg_Nav_Db_Navitem extends Tg_Db_Table_Row
{
	private $_items;
	private $_parent;
	private $_nav;

    /**
     * Initialise a new CMS Navitem object
     *
     * @param  Tg_Nav_Db_Navitem $parentNavitem
     * @param  Tg_Nav_Db_Nav $Pm
     * @return null
     */

	function initNavitem($parentNavitem, $nav) {
		$this->_items = array ();
        $this->_nav = $nav;
        $this->_parent = $parentNavitem;
	}

    /**
     * Recursively populates page with child pages
     *
     * @param  Array $rows
     * @param  Tg_Nav_Db_Nav $nav
     * @return null
     */
    public function populatNavitems ($rows, $nav) {
        $row=$rows->current();
        while ($row!=null && $row->left < $this->right) {
            $currentRow = $row;
            $currentRow->initNavitem ($this, $nav);
            $rows->next ();
            $currentRow->populatNavitems ($rows, $nav);
            $this->_items[] = $currentRow;
            $row=$rows->current();
        }
    }

	/**
	 * Returns the pages ancestors
	 *
	 * @return Array $ancestors
	 */
	function getAncestors () {
		$ancestors = array ($this);
		$parent = $this->_parent;
		while ($parent != null) {
			array_unshift ($ancestors, $parent);
			$parent = $parent->getParent();
		}
		return $ancestors;
	}

    function getPreviousSibling($loop = true)
    {
        $parent = $this->getParent();

        if (!$parent)
            return null;

        $pages = array ();
        foreach ($parent->getNavitems() as $page) {
            if ($page->visible)
            {
                $pages[] = $page;
            }
        }


        $current = 0;
        for ($i=0;$i<count($pages);$i++)
        {
            if ($pages[$i]->id == $this->id)
            {
                $current = $i;
                break;
            }
        }


        if ($loop)
            return $current>0?$pages[$current-1]:$pages[count($pages)-1];
        else
            return $current>0?$pages[$current-1]:null;
    }

    function getNextSibiling ($loop = true)
    {
        $parent = $this->getParent();

        if (!$parent)
            return null;

        $pages = array ();
        foreach ($parent->getNavitems() as $page) {
            if ($page->visible)
            {
                $pages[] = $page;
            }
        }

        $current = 0;
        for ($i=0;$i<count($pages);$i++)
        {
            if ($pages[$i]->id == $this->id)
            {
                $current = $i;
                break;
            }
        }

        if ($loop)
            return $current<count($pages)-1?$pages[$current+1]:$pages[0];
        else
            return $current<count($pages)-1?$pages[$current+1]:null;
    }


	/**
	 * Returns a pages ancestor determined by index
	 *
	 * @return Array $ancestors
	 */
	function getAncestor ($i) {
		$ancestors = $this->getAncestors();
		if (isset($ancestors[$i]))
			return $ancestors[$i];
		else
			return null;
	}

	/**
	 * Returns a subpage
	 *
	 * @return Tg_Nav_Db_Navitem $page
	 */
	function &getNavitem ($pathArray, $strict=false) {
		if (count($pathArray)>0) {
			$pageID = $pathArray[0];
			if (array_key_exists($pageID, $this->_items)) {
				array_shift($pathArray);
				return $this->_items[$pageID]->getNavitem ($pathArray);
			} elseif ($strict) {
				$null = false;
				return $null;
			}
		}
		return $this;
	}

	/**
	 * Get the subpages
	 *
	 * @return array $pages
	 */
    public function getNavitems () {
    	return $this->_items;
    }

	/**
	 * Returns the pages parent
	 *
	 * @return Tg_Nav_Db_Navitem $parent
	 */
	function getParent () {
		return $this->_parent;
	}

    /**
     * Returns true if $Item is an ancestor of $this
     *
     * @param Tg_Nav_Db_Navitem $Item
     * @return boolean $isAncestor
     */
    public function isAncestor ($Item)
    {
    	if ($this->path == '')
    		return false;
    	else
    	{
    		$return = (strpos($Item->path,$this->path)===0);

    		return $return;
    	}
    }

	function appendNavitem (array $data)
	{
		$data['left'] = $this->right;
		$data['right'] = $this->right+1;
		unset($data['id']);

		$pages = new Tg_Nav_Db_Navitems ();

		$page = $pages->createRow($data);

		$left = $pages->getAdapter()->quoteIdentifier('left');
		$right = $pages->getAdapter()->quoteIdentifier('right');

		$pages->update(array('right'=>new Zend_Db_Expr("$right+2")),"$right>=".$this->right);
		$pages->update(array('left'=>new Zend_Db_Expr("$left+2")),"$left>=".$this->right);

		$page->save ();

        $inst = Tg_Nav::getInstance();

		$page->initNavitem ($this, $inst);

		return $page;
	}

	public function update (array $data)
	{
		$this->setFromArray($data);
		$this->save();
	}

	function delete ()
	{
		$pages = new Tg_Nav_Db_Navitems();

		if ($this->left==1)
			throw new Exception ('Can\'t delete ROOT page');
		if ($this->locked)
			throw new Exception('Can\'t delete locked page');

		$left = $pages->getAdapter()->quoteIdentifier('left');
		$right = $pages->getAdapter()->quoteIdentifier('right');

		$dif = ($this->right-$this->left)+1;

		// delete page and all subpages
		$where = array(
			"$left>=".$this->left,
			"$right<=".$this->right);
		$pages->delete ($where);

		// update tree
		$pages->update (array ("left"=>new Zend_Db_Expr("$left-$dif")), "$left>{$this->left}");
		$pages->update (array ("right"=>new Zend_Db_Expr("$right-$dif")), "$right>{$this->right}");
	}

	function moveNavitem ($pageId, $previousSiblingId = 0)
	{
    	$Pm = Tg_Nav::getInstance();
		$pages = new Tg_Nav_Db_Navitems();

		$left = $pages->getAdapter()->quoteIdentifier('left');
		$right = $pages->getAdapter()->quoteIdentifier('right');

		$moveAmount = 0;
		$pageOldLeft = 0;
		$pageOldRight = 0;
		$pageNewLeft = 0;
		$pageNewRight = 0;

		$page = $Pm->getNavitemById($pageId);

		$gap = $page->right - $page->left + 1;

		if ($previousSiblingId != 0)
		{
		    $previousSibling = $Pm->getNavitemById($previousSiblingId);
		    if ($previousSibling->getParent()->id != $this->id)
		        throw new Exception("Sibling is not a child of parent");
		    else
		    {
		        $pageNewLeft = $previousSibling->right + 1;
		        $pageNewRight = $pageNewLeft + $gap - 1;
		    }
		} else {
		    $pageNewLeft = $this->left + 1;
		    $pageNewRight = $pageNewLeft + $gap - 1;
		}

		// amount we have to move $page
		if ($page->left < $pageNewLeft)
		{
		    // $page is further down the tree
		    $pageOldLeft = $page->left;
		    $pageOldRight = $page->right;
		    $moveAmount = $pageNewLeft-$page->left;
		}
		else
		{
		    $pageOldLeft = $page->left + $gap;
		    $pageOldRight = $page->right + $gap;
		    $moveAmount = $pageNewLeft-($page->left + $gap);
		}

		// create a space for new node(s)
		$pages->update (array ("right"=>new Zend_Db_Expr("$right+$gap")), "$right>=$pageNewLeft");
		$pages->update (array ("left"=>new Zend_Db_Expr("$left+$gap")), "$left>=$pageNewLeft");

		// move $page (and sub pages) into gap
		$pages->update (array ("left"=>new Zend_Db_Expr("$left+$moveAmount"),"right"=>new Zend_Db_Expr("$right+$moveAmount")), "$left>=$pageOldLeft AND $right<=$pageOldRight");

		// close gap left by moving $page
		$pages->update (array ("right"=>new Zend_Db_Expr("$right-$gap")), "$right>$pageOldRight");
		$pages->update (array ("left"=>new Zend_Db_Expr("$left-$gap")), "$left>$pageOldLeft");
	}

	function toObject ()
    {
        $pageNode = array (
            'title'=>$this->title,
            'page'=>$this->page,
            'url'=>$this->url,
            'id'=>$this->id,
            'type'=>$this->type,
            'data'=>$this->data,
            'items' => array ()
            );

        if ($this->_items) {
            foreach ($this->_items as $subpage)
            {
                $childNode = $subpage->toObject();
                if ($childNode)
                    $pageNode['items'][]=$childNode;
            };
        }

        return $pageNode;
	}

	public function toJson ()
	{
		return Zend_Json_Encoder::encode($this->toObject ());
	}
}
?>