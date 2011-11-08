<?php
/**
 * Blog Factory Class
 * 
 * Note: This is a singleton class, use Tgx_Blog_Factory::getInstance()
 *
 * @copyright  Copyright (c) 2008 Delete Limited (http://www.deletelondon.com)
 */

class Tgx_Blog_Factory {
	protected static $_instance = null;
	private $_blogs;
	private $_categories;
	private $_postColumns = array('blog_post.id','blog_post.title','blog_post.datePublished','blog_post.slug','blog_post.excerpt','blog_post.authorId','blog_post.file_thumbnail', 'blog_post.comments','blog_post.content');
	private $_options;
	private $_defaults = array (
		'controller' => 'blog',
		'categoryAction'=>'category',
		'postAction'=>'post',
		'postSlugName'=>''
		) ;
	private $_blogId = null;
	
	private function __construct ($options=array()) {
		$this->_options = $options + $this->_defaults;
		
		if (isset($options['blogId']))
			$this->_blogId = $options['blogId'];
	}

    /**
     * Get singleton instance
     *
     * @return Tgx_Blog_Factory
     */
	public static function getInstance($options=array()) {
		if(self::$_instance === null) {
			self::$_instance = new self($options);
		}
		return self::$_instance;
	}
	
	function getPostUrl($post) {
		// TODO - change to add site path prefix
		$return = '/';
		if ($this->_options['controller'])
			$return .= $this->_options['controller'].'/';
		if ($this->_options['postAction'])
			$return .= $this->_options['postAction'].'/';
		if ($this->_options['postSlugName'])
			$return .= $this->_options['postSlugName'].'/';
		$return .= $post->slug;
		
		return $return;
	}
	
	function getCategoryUrl($category) {
		// TODO - change to add site path prefix
		$return = '/';
		if ($this->_options['controller'])
			$return .= $this->_options['controller'].'/';
		if ($this->_options['postAction'])
			$return .= $this->_options['categoryAction'].'/';
		$return .= $category->slug;
		
		return $return;
	}

    /**
     * Get all blog posts
     *
     * @param  boolean $reload
     * @return Tgx_Blog_Blogs_Rowset
     */
	function getBlogs ($reload=false)
	{
		if ($this->_blogs && !$reload)
			return $this->_blogs;

		$blogs = new Tgx_Blog_Blogs();

		$blogsRowset = $blogs->fetchAll();

		foreach ($blogsRowset as $blog) {
			$this->_blogs[$blog->id] = $blog;
		}

		return $this->_blogs;
	}
	
	function getBlogNames ()
	{
		$blogs = array ();
		$blogsRowset = $this->getBlogs();
		foreach ($blogsRowset as $blog)
				$blogs[$blog->id] = $blog->name;
		return $blogs;
	}

    /**
     * Get a blog using the id
     *
     * If slug is null it returns a new blank post
     *
     * @param  sring $id
     * @return Tgx_Blog_Blogs_Row
     */
	function &getBlogById ($id)
	{
		if (isset($this->_blogs[$id]))
			return $this->_blogs[$id];

		$blogs = new Tgx_Blog_Blogs();
		$blog = $blogs->fetchRow("id=$id");

		if ($blog) {
			$this->_blogs[$blog->id] = $blog;
			return $blog;
		} else
			throw new Zend_Exception("Blog not found ($id)");
	}

    /**
     * Get a blog using the url slug
     *
     * If slug is null it returns a new blank post
     *
     * @param  sring $slug
     * @return Tgx_Blog_Blogs_Row
     */
	function &getBlogBySlug ($slug)
	{
		// TODO - add code to check blog cache ($this->_blogs)
		$blogs = new Tgx_Blog_Blogs();
		$blog = $blogs->fetchRow("slug='$slug'");
		if ($blog != null)
			return $blog;
		else
			throw new Zend_Exception("Blog not found ($slug)");
	}

    /**
     * Get all blog posts
     *
     * @param  none
     * @return Zend_Db_Table_Rowset_Abstract
     */
	function getPosts ($limit = null, $page = null)
	{	
		$posts = new Tgx_Blog_Posts();
		
		// adds commentCount column to rowset
		if(defined('DELETE_DB') && DELETE_DB == 'pdo_odbc') {
			// MS SQL doesn't seem to like the group by stuff ... ignore comments	
			$select = $posts->select()->setIntegrityCheck(false);
		} else {
			$select = $posts->select()->setIntegrityCheck(false);
			$select->from ($posts);
			$select->joinLeft('comment', 'comment.refTableId=blog_post.id AND comment.refTable=\'blog\'', array('COUNT(comment.id) as commentCount'));
			$select->joinLeft('comment as commentUnpublished', 'commentUnpublished.id=comment.id AND commentUnpublished.published=\'no\'', array('COUNT(commentUnpublished.id) as commentCountUnpublished'));
			$select->group (array('blog_post.id'));
			if ($this->_blogId)
				$select->where ("blog_post.blogId=".$this->_blogId);
		
			if (isset($limit) && isset($page)) {
				$select->limitPage($page , $limit);
			} elseif (isset($limit)) {
				$select->limit($limit);
			}	
		}	
		
		$select->order ('datePublished DESC');
		
		return $posts->fetchAll($select);
	}

	function getNumberOfPosts ($publishedOnly = false)
	{
		$table = new Tgx_Blog_Posts();
		$select = $table->select();
		$select->from(array('bp' => 'blog_post'), array('count' => 'COUNT(DISTINCT(bp.id))'));
		if($publishedOnly) {
			$select->where('bp.published = "yes"');
		}
//		if ($this->_blogId)
//			$select->where ("blogId=".$this->_blogId);	
		$countQuery = $table->fetchRow($select);
		return $countQuery->count;
	}

    /**
     * Get all blog posts
     *
     * @param  none
     * @return Zend_Db_Table_Rowset_Abstract
     */
	function getPublishedPosts ($limit = null, $page = null, $category = null)
	{
		$posts = new Tgx_Blog_Posts();
		
		if(defined('DELETE_DB') && DELETE_DB == 'pdo_odbc') {
			// MS SQL doesn't seem to like the group by stuff ... ignore comments	
			$select = $posts->select()->setIntegrityCheck(false);
		} else {	
			// adds commentCount column to rowset
			$select = $posts->select()->setIntegrityCheck(false);
			$select->from($posts, $this->_postColumns);
			$select->joinLeft('comment', 'comment.refTableId=blog_post.id AND refTable=\'blog\' and comment.published=\'yes\'', array('COUNT(comment.id) as commentCount'));
			$select->group ('blog_post.id');
		}
		$select->where ("blog_post.published='yes'");
		if ($this->_blogId)
			$select->where ("blogId=".$this->_blogId);			
		
		$select->order ('datePublished DESC');
        
        /** If a category has been specified we should fetch all the posts
        that are linked to it **/
        if($category != null){
        	$category = $this->getCategoryBySlug($category);
            $select->join('blog_category_blog_post','blog_category_blog_post.blog_post_id = blog_post.id',null);
            $select->join('blog_category','blog_category.id = blog_category_blog_post.blog_category_id',null);
            $select->where('blog_category.id = '.$category->id.' OR blog_category.parentId = '.$category->id);
        }

		if (isset($limit) && isset($page)) {
			$select->limitPage($page, $limit);
		} elseif (isset($limit)) {
			$select->limit($limit);
		}
		
		// echo $select;

		return $posts->fetchAll($select);
	}

    /**
     * Get all blog posts
     *
     * @param  none
     * @return Zend_Db_Table_Rowset_Abstract
     */
	function getFeaturedPosts ($limit = 5, $published = true)
	{
		$posts = new Tgx_Blog_Posts();
		
		$select = $posts->select()->setIntegrityCheck(false)
			->where ("blog_post.published='yes'")
			->order ('datePublished DESC');
		
		if ($this->_blogId)
			$select->where ("blogId=".$this->_blogId);
		if ($published)
			$select->where ("blog_post.featured='yes'");
		if (isset($limit))
			$select->limit($limit);
		
		return $posts->fetchAll($select);
	}

    /**
     * Gets all blog posts in a particular category
     *
     * @param  int $categoryId
     * @return Zend_Db_Table_Rowset_Abstract
     */
	function getPostsByCategory ($categoryId)
	{
		$posts = new Tgx_Blog_Posts();

		// adds commentCount column to rowset
		$select = $posts->select()->setIntegrityCheck(false);
		$select->from ($posts);
		$select->joinLeft('blog_comment', 'blog_comment.postId=blog_post.id', array('COUNT(blog_comment.id) as commentCount'));
		$select->group ('blog_post.id');
		$select->where ("published='yes'");
		$select->where ("categoryId=$categoryId");
		if ($this->_blogId)
			$select->where ("blog_post.blogId=".$this->_blogId);	
		$select->order ('datePublished DESC');

		return $posts->fetchAll($select);
	}

    /**
     * Get a blog post
     *
     * If postId is null it returns a new blank post
     *
     * @param  int $postId
     * @return Tgx_Blog_Posts_Row $post
     */
	function getPost ($postId, $create=false)
	{
		$posts = new Tgx_Blog_Posts();
		if ($postId) {
			$blogPost = $posts->fetchRow("id=$postId");
		} else if ($create) {
			$blogPost = $posts->createRow();
			$user = Tg_Auth::getAuthenticatedUser();
			$blogPost->authorId = $user->id;
			$blogPost->datePublished = Zend_Date::now();
			$blogPost->save();
		} else
			return false;
    	
    	return $blogPost;
	}

    /**
     * Get a blog post using the url slug
     *
     * If slug is null it returns a new blank post
     *
     * @param  sring $slug
     * @return Tgx_Blog_Posts_Row $post
     */
	function getPostBySlug ($slug, $create=false)
	{
		$posts = new Tgx_Blog_Posts();
		if ($slug)
			$blogPost = $posts->fetchRow($posts->select()->where('slug = ?', $slug));
		
		if (!$blogPost && $create)
			return $posts->createRow();
    	
    	return $blogPost;
	}

    /**
     * Get a blog posts postion from the total number of blog posts
     *
     * If post is not found it returns false
     *
     * @param  int $postId
     * @param  int $blogId
     * @return Zend_Db_Table_Row_Abstract
     */
	function getPostPosition ($postId, $order='datePublished DESC')
	{
		// couldn't figure out a better way to do this ....
		$db =  Zend_Registry::get('db');
		$db->query('SET @rownum := 0');
		$sql = 'SELECT * FROM (SELECT @rownum := @rownum+1 AS rank, id FROM blog_post WHERE published = "yes" ORDER BY '.$order.') AS derived_table WHERE id = "'.$postId.'"';
		$result = $db->query($sql);
		if($row = $result->fetch()) {
			return $row['rank'];
		} else {
			return false;
		}
	}

    /**
     * Gets all blog categories
     *
     * @param  none
     * @return array $categories
     */
	function getCategories ($reload=false)
	{
		if (!isset($this->_categories) || $reload) {
			$this->_categories = array ();
			$categories = new Tgx_Blog_Categories();
            
            //Get all the categories
            $select = $categories->select()->order('position ASC');
            $categoriesRowset = $categories->fetchAll($select);
                        
			$this->_categories = array ();
			foreach ($categoriesRowset as $category)
				$this->_categories[$category->id] = $category;
		}
        
		return $this->_categories;
	}

    /**
     * Gets root categories
     *
     * @param  none
     * @return array $categories
     */
	function getRootCategories ($reload=false)
	{
		if (!isset($this->_rootCategories) || $reload) {
			$this->_rootCategories = array ();
			$categories = new Tgx_Blog_Categories();
            
            //Get all the categories
            $select = $categories->select()->where('parentId=0')->order('position ASC');
            $categoriesRowset = $categories->fetchAll($select);
                        
			$this->_rootCategories = array ();
			foreach ($categoriesRowset as $category)
				$this->_rootCategories[$category->id] = $category;
		}
        
		return $this->_rootCategories;
	}
	
	function getCategoryNames ()
	{
		$categories = array ();
		$categoriesRowset = $this->getCategories();
		foreach ($categoriesRowset as $category)
				$categories[$category->id] = $category->name;
		return $categories;
	}

    /**
     * Gets a blog category
     *
     * If $categoryId is null it returns a new blank category
     *
     * @param  int $categoryId
     * @return Zend_Db_Table_Row_Abstract
     */
	function getCategory ($categoryId)
	{
		$categories = new Tgx_Blog_Categories();
		if ($categoryId) {
			return $categories->fetchRow("id=$categoryId");
		} else
			return $categories->createRow();
	}

    /**
     * Gets a blog category
     *
     * If $categoryId is null it returns a new blank category
     *
     * @param  int $categoryId
     * @return Zend_Db_Table_Row_Abstract
     */
	function getCategoryBySlug ($slug)
	{
		$categories = new Tgx_Blog_Categories();
		$category = $categories->fetchRow("slug='$slug'");
		if ($category != null)
			return $category;
		else
			throw new Zend_Exception("Category not found ($slug)");
	}

    /**
     * Saves a category
     *
     * If the category doesn't have an ID it inserts a new category
     *
     * @param  Zend_Db_Table_Abstract $category
     * @param  Zend_Form $form
     */
	function saveCategory ($category, $form) {
        	$values = $form->getValues ();
        	if (!$category->id)
				unset($values['id']);
			unset($values['submit']);
			$category->setFromArray($values);
			$category->save();
	}
    
    function updateSelectedCategories($blog_id, $categories){
        if($blog_id){
            $db = Zend_Registry::get('db');
            if($db->query("DELETE FROM blog_category_blog_post WHERE blog_post_id = ".$blog_id)){
                if(!empty($categories)){
                    foreach($categories as $category){
                        $data['blog_post_id'] = $blog_id;
                        $data['blog_category_id'] = $category;
                        $db->insert('blog_category_blog_post',$data);
                    }
                }
                return true;
            }else{
                return false;
            }
        }else{
            return false;
        }
    }

	function getArchive () {		
		$posts = new Tgx_Blog_Posts();
		$select = $posts->select();
		$select->from ($posts);
		$select->where ("published = 'yes'");
		if ($this->_blogId)
			$select->where ("blogId=".$this->_blogId);
		$select->order ('datePublished DESC');

		$allPosts = $posts->fetchAll($select);		
		
		$archive = array();
		foreach($allPosts as $allPost) {
			$date = strtotime($allPost->datePublished);
			$archive[date('Y', $date)][date('F', $date)][] = $allPost;
		}
		
		return $archive;
	}
}
?>
