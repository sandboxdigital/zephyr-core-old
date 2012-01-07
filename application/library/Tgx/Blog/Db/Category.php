<?php

class Tgx_Blog_Db_Category extends Tg_Db_Table_Row
{
	function getPosts ()
	{
		$posts = new Tgx_Blog_Posts();
		
		// adds commentCount column to rowset
		$select = $posts->select()->setIntegrityCheck(false);
		$select->from($posts, array('blog_post.id','blog_post.title','blog_post.datePublished','blog_post.slug','blog_post.excerpt','blog_post.authorId','blog_post.file_thumbnail', 'blog_post.comments'));
		$select->joinLeft('comment', 'comment.refTableId=blog_post.id AND refTable=\'blog\' and comment.published=\'yes\'', array('COUNT(comment.id) as commentCount'));
		$select->group ('blog_post.id');
		$select->where ("blog_post.published='yes'");
		$select->order ('datePublished DESC');
        
		$select->join('blog_category_blog_post','blog_category_blog_post.blog_post_id = blog_post.id',null);
//		$select->join('blog_category','blog_category.id = blog_category_blog_post.blog_category_id',null);
		$select->where('blog_category_blog_post.blog_category_id = '.$this->id);
		// echo $select;

		return $posts->fetchAll($select);
	}
	
	function getPostCount ()
	{
		$count = 0;
		
//		foreach($category_res as $cat){
//                    //How many posts are within this category 
//                    $post_amm_res = $db->fetchRow("SELECT count(bp.id) AS amm FROM blog_post AS bp
//                                                   JOIN blog_category_blog_post AS bcbp ON bcbp.blog_post_id = bp.id
//                                                   WHERE bp.published = 'yes'
//                                                   AND bcbp.blog_category_id = ".$cat['id']);
//
//                    $cat['postCount'] = $post_amm_res->amm;
//                    
//                    $this->_categories[$cat['id']] = (Object)$cat;
//                }
//            }
            
		// TODO - fix!
		return $count;
	}
	
	function getSubCategories ()
	{
		return $this->_table->fetchAll($this->_table->select(true)
		->where('parentId='.$this->id));
	}
}