<?php
/**
 * Tg Site Page Database Gateway Class
 *
 * @copyright  Copyright (c) 2009 Thomas Garrood (http://www.garrood.com)
 *
 */

class Tg_Site_Db_Page extends Tg_Db_Table_Row implements Zend_Acl_Resource_Interface
{
	private $_path;
	private $_pages;

	private $_parent;
	private $_template;

	private $_properties;

    /**
     * Initialise a new CMS Page object
     *
     * @param  Delte_Pm_Db_Page $parentPage
     * @param  Delte_Pm_Facade $Pm
     * @return null
     */

    public function __get($name) {
    	$config = Zend_Registry::get('config');

        switch ($name) {
        	case 'metaTitle':
        		if (!empty($this->_data['metaTitle']))
        			return $this->_data['metaTitle'];
        		else {
	        		return $config['meta']['title'].$this->title;
        		}
        		break;
        	case 'metaDescription':
        		if (!empty($this->_data['metaDescription']))
        			return $this->_data['metaDescription'];
        		else {
    				if (!empty($config['meta']['description']))
		        		return $config['meta']['description'];
        		}
        		return '';
        		break;
        	case 'metaKeywords':
        		if (!empty($this->_data['metaKeywords']))
        			return $this->_data['metaKeywords'];
        		else {
    				if (!empty($config['meta']['keywords']))
		        		return $config['meta']['keywords'];
        		}
        		return '';
        		break;
        	case 'template':
        	case 'Template':
        		return $this->getTemplate ();
        	case 'pages':
        	case 'Pages':
        		return $this->getPages ();
        	case 'path':
        		return $this->_path;
        	case 'url':
        		return $this->getUrl();
        	case 'properties':
        		return $this->getProperties();
        }
        if (array_key_exists($name, $this->_data)) {
            return $this->_data[$name];
        }

        if ($this->_properties) {
	        if (in_array($name, $this->_properties->keys())) {
	            return $this->_properties->$name;
	        }
        }

        throw new Zend_Exception('Unknown Tg_Site_Db_Page value or property: '.$name);
    }

    public function __toString() {
    	return $this->path;
    }

	function initPage(&$parentPage, Tg_Site_Factory &$Pm) {
		$this->_pages = array ();

		if ($parentPage) {
			$this->_parent = $parentPage;
			if ($this->_parent->path == '/')
				$this->_path = $this->name;
			else
				$this->_path = $this->_parent->path.'/'.$this->name;
		} else {
			$this->_parent = null;
			$this->_path = '/';
		}

		$this->_pages = array ();

		$this->_template = $Pm->getTemplate($this->templateId); // Pm has to be passed to us as using Tg_Site::getInstance will create a continuous loop
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
        foreach ($parent->getPages() as $page) {
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
        foreach ($parent->getPages() as $page) {
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
	 * @return Tg_Site_Db_Page $page
	 */
	function &getPage ($pathArray, $strict=false) {
		if (count($pathArray)>0) {
			$pageID = $pathArray[0];
			if (array_key_exists($pageID, $this->_pages)) {
				array_shift($pathArray);
				return $this->_pages[$pageID]->getPage ($pathArray);
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
    public function getPages () {
    	return $this->_pages;
    }

	/**
	 * Returns the pages parent
	 *
	 * @return Tg_Site_Db_Page $parent
	 */
	function getParent () {
		return $this->_parent;
	}

	/**
	 * Returns the pages parent
	 *
	 * @return Tg_Site_Db_PageTemplate $parent
	 */
	public function getTemplate () {
		return $this->_template;
	}


	/**
	 * Enter description here ...
	 *
	 * @return Tg_Content_Data $data
	 */
	public function getContent ($version = 0)
	{
		$content = $this->getContentRecord($version);

    	if ($content->data == '<data></data>')
    	{
			if (!empty($this->dataXML))
			{
	    		$content->data = $this->dataXML;
	    		$content->save ();
			}
    	}

	    return $content->content();
	}

	/**
	 * Enter description here ...
	 *
	 * @return Tg_Content_Data $data
	 */
	public function getContentRecord ($version = 0)
	{
		$contentId = 'SitePage'.$this->id;
		return Tg_Content::getContent($contentId, $version);
	}

	/**
	 * Returns the pages theme
	 *
	 * @return Tg_Site_Db_Theme $theme
	 */
	public function getTheme ()
	{
		return Tg_Site::getTheme($this->themeId);
	}

    public function getPath ()
    {
        return $this->_path;
    }

	public function getUrl ($lang = null)
	{
		$prefix = Tg_Site::pathPrefix ($lang);
		return str_replace('//','/',$prefix.''.$this->_path);
	}

	function &getPageById ($id) {
		$array_keys = array_keys($this->_pages);

		for ($i=0;$i<count ($array_keys);$i++) {
			if ($this->_pages[$array_keys[$i]]->id==$id)
				return $this->_pages[$array_keys[$i]];

			$return = $this->_pages[$array_keys[$i]]->getPageById ($id);
			if ($return != null)
				return $return;
		}

		$false = false;

		return $false;
	}

    /**
     * Recursively populates page with child pages
     *
     * @param  Array $rows
     * @param  Delte_CMS_Factory $cms
     * @return null
     */
	public function populatPages (&$rows, &$Pm) {
		$row=$rows->current();
		while ($row!=null && $row->left < $this->right) {
			$currentRow = $row;
			$this->_pages[$currentRow->name] = $currentRow;
			$this->_pages[$currentRow->name]->initPage ($this, $Pm);
			$rows->next ();
			$this->_pages[$currentRow->name]->populatPages ($rows, $Pm);
			$row=$rows->current();
		}
	}

    /**
     * Returns true if $Page is an ancestor of $this
     *
     * @param Tg_Site_Db_Page $Page
     * @return boolean $isAncestor
     */
    public function isAncestor ($Page)
    {
    	if ($this->path == '')
    		return false;
    	else
    	{
    		$return = (strpos($Page->path,$this->path)===0);

    		return $return;
    	}
    }

	function appendPage (array $data)
	{
		$data['left'] = $this->right;
		$data['right'] = $this->right+1;
		if ($data['name'])
			$data['name'] = Tg_Helpers_Url::sanitiseUrl($data['name']) ;
		elseif ($data['title'])
			$data['name'] = Tg_Helpers_Url::sanitiseUrl($data['title']) ;
		unset($data['id']);

		$pages = new Tg_Site_Db_Pages ();

		$page = $pages->createRow($data);

		$left = $pages->getAdapter()->quoteIdentifier('left');
		$right = $pages->getAdapter()->quoteIdentifier('right');

		$pages->update(array('right'=>new Zend_Db_Expr("$right+2")),"$right>=".$this->right);
		$pages->update(array('left'=>new Zend_Db_Expr("$left+2")),"$left>=".$this->right);

		$page->save ();

		if ($page->action = '""')
		{
			// silly bug in database SQL sets default to ""
			$page->action = '';
			$page->save();
		}

        $inst = Tg_Site::getInstance();

		$page->initPage ($this, $inst);

		return $page;
	}

	public function update (array $data)
	{
		if ($data['name'])
			$data['name'] = Tg_Helpers_Url::sanitiseUrl ($data['name']) ;
		elseif ($data['title'])
			$data['name'] = Tg_Helpers_Url::sanitiseUrl ($data['title']) ;

		$this->setFromArray($data);
		$this->save();
	}

	function delete ()
	{
		$pages = new Tg_Site_Db_Pages();

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

	function movePage ($pageId, $previousSiblingId = 0)
	{
    	$Pm = Tg_Site::getInstance();
		$pages = new Tg_Site_Db_Pages();

		$left = $pages->getAdapter()->quoteIdentifier('left');
		$right = $pages->getAdapter()->quoteIdentifier('right');

		$moveAmount = 0;
		$pageOldLeft = 0;
		$pageOldRight = 0;
		$pageNewLeft = 0;
		$pageNewRight = 0;

		$page = $Pm->getPageById($pageId);

		$gap = $page->right - $page->left + 1;

		if ($previousSiblingId != 0)
		{
		    $previousSibling = $Pm->getPageById($previousSiblingId);
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

	function getResourceId ()
	{
		return $this->_path;
	}

	function getRoles ()
	{
		return Tg_Site_Acl::getPageRoles($this->id);
	}

	function toStdObject ($requiredPrivs = 'write', $children=true)
    {
    	$user = Tg_Auth::getAuthenticatedUser();
		$allowed = Tg_Site_Acl::isUserAllowed($user, $this->path, $requiredPrivs);
		if ($allowed)
		{
			$pageNode = array (
		    	'title'=>$this->title,
		    	'id'=>$this->id,
		    	'name'=>$this->name,
		    	'templateId'=>$this->templateId,
		    	'themeId'=>$this->themeId,
				'visible'=>$this->visible,
				'action'=>$this->action,
				'pages' => array ()
		    	);
		    	
		    if (isset($this->metaTitle))
		    	$pageNode['metaTitle'] = $this->metaTitle;
		    if (isset($this->metaKeywords))
		    	$pageNode['metaKeywords'] = $this->metaKeywords;
		    if (isset($this->metaDescription))
		    	$pageNode['metaDescription'] = $this->metaDescription;

		    if ($this->_pages && $children) {
		    	foreach ($this->_pages as $subpage)
		    	{
		    		$childNode = $subpage->toStdObject($requiredPrivs);
		    		if ($childNode)
		    			$pageNode['pages'][]=$childNode;
		    	};
		    }


            $roles = $this->getRoles();

            $pageNode['roles'] = $roles;

	    	return $pageNode;
		} else
			return null;
	}

	public function toJson ($requiredPrivs='write')
	{
		return Zend_Json_Encoder::encode($this->toStdObject ($requiredPrivs));
	}
}
?>