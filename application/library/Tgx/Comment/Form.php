<?php
class Tgx_Comment_Form extends Tg_Form  
{ 
	private $_defaults = array (
		'userRequired'=>true,
		'captchaRequired'=>false,
		'method'=>'post',
		'action'=>'/comment/add',
		'useAjax'=>'true'
	);
	
    public function __construct($options=array()) 
    {
		$options = $options + $this->_defaults;
    	parent::__construct ($options);   

    	$this->setMethod($options['method']);
		
		$this->addElement('hidden', 'id', array ('decorators'=>array('ViewHelper')));
		
		$user = Tg_Auth::getAuthenticatedUser();
					
		if ($options['userRequired'] && !$user) {
			$this->addElement('text', 'name',
				array (
					'class'=>'text', 
					'label'=>'Name:', 
					'required'=>true
				)
			);
			$this->addElement('text', 'email', 
				array (
					'class'=>'text', 
					'label'=>'Email:', 
					'required'=>true, 
					'validators' => array('EmailAddress')
				)
			);
		}
		
		if ($options['captchaRequired']) {
			$this->addElement('captcha', 'humanoid', array (
				'label'=>'Are you human?',
				'captcha' => array (
					'captcha' => 'Image',
					'wordLen' => 3,
					'fontSize' => 40,
					'font' => Zend_Registry::getInstance()->get('siteconfig')->comment->captcha_font,
					'imgDir' => Zend_Registry::getInstance()->get('siteconfig')->comment->captcha_cachedir
				),
			));
		}
		
		$this->addElement('textarea', 'comment', array ('label'=>'Comment:', 'required'=>true, 'class'=>'textarea'));
		
		$this->addElement('submit', 'submit', array (
			'label' => 'Save Comment >>',
			'class' => 'submit'
		));
    } 
} 
?>