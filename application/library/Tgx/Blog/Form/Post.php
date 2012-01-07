<?php

class Tgx_Blog_Form_Post extends Tg_Form
{
	private $_defaults = array (
		'showAuthors'=>false,
		'showComments'=>false,
		'showBlogs'=>true,
		'showCategories'=>true,
		'showFeatured'=>true,
		'showThumbnail'=>true	
	);
			
    public function __construct(Tgx_Blog_Posts_Row $BlogPost, $options=array())
    {
    	parent::__construct (array('disableLoadDefaultDecorators' => true));
		$options = $options + $this->_defaults;	
    	
    	$this->setAction('/admin/blog/post-edit')
		     ->setMethod('post');

		$this->addElement('hidden', 'id', array ('decorators'=>array('ViewHelper')));
		
		$this->addElement('text', 'title', array(
			'label'=>'Title',
			'required'   =>true,
			'class'		 => 'text'
			));
			
		$this->addElement('textarea', 'excerpt', array(
			'label'=>'Abstract',
			'style'=>'height:50px;',
				'validators' 	=> array (
					//array('WordLength', true, array(0, 37)),
					array('StringLength', false, array(0, 250))
				)
			));
			
		// blog
		$blogs = Tgx_Blog::getInstance ()->getBlogNames();
		if (count($blogs)>1 && $options['showBlogs']) {
			$this->addElement('select', 'blogId', array(
				'label'=>'Type',
				'required'=>true,
				'multiOptions'=>$blogs
				));
		} else {
			$aKeys = array_keys($blogs);
			$this->addElement('hidden', 'blogId', array(
				'label'=>'News type',
				'required'=>true,
				'value'=>$aKeys[0],
				'decorators'=>array('ViewHelper')
				));
		}

		$this->addElement('jqueryDate','datePublished', array(
			'label'=>'Date published',
			'required'=>true,
			'class'=>'textsmall'
			));			

		$this->addElement('select', 'published', array(
			'label'=>'Published',
			'required'=>true,
			'multiOptions'=>array (
				'yes'=>'Yes', 'no'=>'No'
				)
			));	
						
		// author
		if ($options['showAuthors']) {
			$users = Tg_User::getUserNames (array (1));		
			$this->addElement('select', 'authorId', array(
				'label'=>'Author',
				'required'=>true,
				'multiOptions'=>$users
				));	
		} else {
			$this->addElement('hidden', 'authorId', array('value'=>'0','decorators'=>array('ViewHelper')));
		}
						
		// on homepage
		if ($options['showFeatured']) {	
			$this->addElement('select', 'featured', array(
				'label'=>'Featured post',
				'required'=>false,
				'value'=>'no',
				'multiOptions'=>array (
					'no'=>'No',
					'yes'=>'Yes'
					)
				));	
		}
		
        // categories
		if ($options['showCategories']) {
			$categories = Tgx_Blog::getInstance()->getCategoryNames();
	        $selected_categories = $BlogPost->getCategoriesIds();
	        $this->addElement('multiCheckbox','categories', array(
				'label'=>'Categories',
				'multiOptions'=>$categories,
	        	'value'=>$selected_categories
				));
		}
			
		if ($options['showComments']) {
			$this->addElement('select', 'comments', array(
				'label'=>'Comments',
				'required'=>true,
				'multiOptions'=>array (
					'yes'=>'Yes', 'no'=>'No'
					)
				));
		} else {
			$this->addElement('hidden', 'comments', array('value'=>'No','decorators'=>array('ViewHelper')));
		}
		
		$this->addElement('tgFileUpload','file_thumbnail', array(
			'label'=>'Thumbnail'
			));
		
		$this->addElement('content','content', 
			array (
			'form'=>'blog.xml',
			'decorators'=>array('ViewHelper')
			));
//			
//		$content = $this->getElement ('content');
//		$$content->setDecorators(array(
//		    'FormElements'
//		));
    	
		$this->addElement('submit','save', array ('class'=>'submit', 'label' => 'Save'));
		    	
		$this->addDisplayGroup(array('blogId', 'datePublished', 'comments', 'published', 'featured', 'categories', 'file_thumbnail', 'authorId'),'right');
		// add cms block to form
		$this->addDisplayGroup(array('title','excerpt','content'),'left');
		
		$this->addDisplayGroup(array('save'),'buttons');			
		
		$this->setDisplayGroupDecorators(array(
		    'FormElements',
			array('HtmlTag', array('tag' => 'dl')),
		    'Fieldset'
		));		
		
		$this->setDecorators(array(
		    'FormElements',
		    'Form'
		));
    }
}
?>