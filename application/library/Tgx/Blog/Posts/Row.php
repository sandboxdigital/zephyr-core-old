<?php

class Tgx_Blog_Posts_Row extends Tg_Db_Table_Row
{
	protected $_name = 'blog_post';
	private $_blog;
	private $_block;

    /**
     * Get the comments for this post
     *
     * @return Zend_Db_Table_Rowset_Abstract
     */
	public function getComments () {
		return Tgx_Comment::getInstance()->getComments ('BLOG', $this->id);
	}
	
    /**
     * Getter
     *
     * @return $var
     */
	public function __get ($name) {
		switch ($name) {
			case 'blog':
			case 'Blog':
				return $this->getBlog();
				break;
			case 'url':
				return $this->getUrl();
				break;
			case 'Author':
				return $this->getAuthor();
				break;
			case 'Thumbnail':
				return $this->getThumbnail();
				break;
			case 'commentCount':	
			case 'commentCountUnpublished':
				if (isset($this->_data[$name]))
					return $this->_data[$name];
				else
					return '0';
				break;
			default:
				return parent::__get($name);			
		}	
	}
	
	/**
	 * Returns the thumbnail of the blog post
	 * 
	 * @return $file Delete_File
	 */
	public function getThumbnail () {
		if ($this->file_thumbnail>0)
			return Tg_File::getFileById($this->file_thumbnail);
		else
			return null;
	}
	
	/**
	 * Gets the comments author
	 *
	 * @return $user
	 */
	function getAuthor () {
		return Tg_User::getInstance()->getUserById ($this->authorId, true);
	}

    /**
     * Get the blog post before this
     *
     * @return Zend_Db_Table_Row_Abstract
     */
	public function getPrevious ()
	{
		$posts = new Tgx_Blog_Posts();
		return $posts->fetchRow("datePublished<'".$this->datePublished."' AND blogId=".$this->blogId, 'datePublished DESC LIMIT 1');
	}

    /**
     * Get the blog post after this
     *
     * @return Zend_Db_Table_Row_Abstract
     */
	public function getNext ()
	{
		$posts = new Tgx_Blog_Posts();
		return $posts->fetchRow("datePublished>'".$this->datePublished."' AND blogId=".$this->blogId, 'datePublished ASC LIMIT 1');
	}

    /**
     * Get the blog post after this
     *
     * @return $url string
     */
	public function getUrl ()
	{
		return Tgx_Blog_Factory::getInstance()->getPostUrl($this);
	}

    /**
     * Get the blog for this post
     *
     * @return $blog Tgx_Blog_Blogs_Row
     */
	public function getBlog ()
	{
		if ($this->blogId)
			return Tgx_Blog_Factory::getInstance()->getBlogById($this->blogId);
		else
			return null;
	}

    /**
     * Saves a post
     *
     * If the post doesn't have an ID it inserts a new post
     *
     * @param  Zend_Form $form
     */
	function updateFromForm ($Form) {
        $values = $Form->getValues ();

		if (isset($values['slug'])) {
			$this->slug=$values['slug'];
		} else {
			$this->slug = $this->_sanitiseSlug($values['title']);
		}
		
		$this->setFromArray($values);
        $this->save();

		// cms block
		
		// categories
		 //update the selected categories for this blog post
		 // why are we using $_POST!!!!!
         Tgx_Blog::getInstance()->updateSelectedCategories($this->id, $_POST['categories']);
	}
	
	/**
	 * Returns the categories assigned to this blog post
	 *
	 * @return array $categories 
	 */
	public function getCategories () {
		$categories = Tgx_Blog::getInstance()->getCategories();
		
		$ids = $this->getCategoriesIds();
		$return = array ();
		foreach ($ids as $id)
			$return[$id] = $categories[$id];
		
		return $return;
	}
	
	public function getCategoriesIds() {
        $data = array();
                
		$db = Zend_Registry::get('db');
		if($res = $db->fetchAll('SELECT blog_category_id FROM blog_category_blog_post WHERE blog_post_id = '.$this->id)){
			foreach($res as $k => $v){
				$data[] = $v->blog_category_id;
			}
		}
		
		return $data;
    }
    
    /**
     * Get CMS Block associated with this post
     * note will create a blank block if one doesn'y exist
     *
     * @return Delete_Cms_Block $block
     */
    public function getContent ($contentName='blogContent') {
	   return Tg_Content::loadXml ($this->content)->$contentName;
    }
    
    public function delete ()
    {
    	$db = Zend_Registry::get('db');
        $db->query("DELETE FROM blog_category_blog_post WHERE blog_post_id = ".$this->id);

        parent::delete ();
    }

	private function _sanitiseSlug ($slug) {
		
		$name =  strtolower($slug);
		$name = str_replace(' ','-', $name);
		
		$filtered_name = '';
		for ($i=0;$i<strlen($name);$i++) {
			$current_char = substr($name,$i,1);
			if (ctype_alnum($current_char) == TRUE || $current_char == "_" || $current_char == "-") {
				$filtered_name .= $current_char;
			}
			else {
				$filtered_name .= '-';
			}
		}		
		
		return $filtered_name;		
	}

}
?>