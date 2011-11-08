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
 * Tg Form Class
 */

class Tg_Form extends Zend_Form
{
	private $_options;
	
	private $_defaults = array(
		'useAjax'=>false
	);
	
	public function __construct($options = array())
	{
		if (!is_array($options))
			$options = array();
		
		$this->_options = array_merge($this->_defaults, $options);
		
			
		if ($this->_options['useAjax'])
		{
			if (!isset($this->_options['id']))
				$this->_options['id'] = 'form-'.rand(10000, 99999);
			
		}
		
		parent::__construct($this->_options);
		
		// add error summary decorator (will list all validation errors at the
		// top of the form - all 'Error' decorators should be disabled since we
		// are not showing the errors next to the input item (just turning the
		// labels red)
		$this->addDecorator(new Tg_Form_Decorator_FormErrors(
		array('placement'=>Zend_Form_Decorator_Abstract::PREPEND,
                    'message'=>'account_error_summary')));
		
		$this->addElementPrefixPath('Tg_Validate', 'Tg/Validate/', 'validate');
		$this->addPrefixPath('Tg_Form_Element', 'Tg/Form/Element/', 'element');
		$this->addPrefixPath('Tg_Form_Decorator', 'Tg/Form/Decorator/', 'decorator');

		$this->setMethod('post');
		$this->setAttrib('enctype', 'multipart/form-data');
	}	
	
	function process ($request = null) 
	{
		
		if (empty($request))
		{
			$frontController = Zend_Controller_Front::getInstance();
			$request = $frontController->getRequest();			
		}
		
		if($request->isPost()) {
			if($this->isValid($request->getPost())) {
				
				$this->onValid();
				
				return true;
			}
		}		
		return false;
	}
	
	function onValid ()
	{
		// override me
	}
	
	function onError ()
	{

		
	}
	
	function __toString()
	{
		$return = parent::__toString();
		
		if ($this->_options['useAjax'])
		{
			$id = $this->getId();
			$action = $this->getAction();
			
			$pre = '<div id="container-'.$id.'">';
			
			
			$post = '</div><script type="text/javascript">		
			$(document).ready(function ()
			{
				$("#'.$id.'").submit (function(){
					var data = $(this).serialize();
					$.post("'.$action.'",data,function(ret){
						$("#container-'.$id.'").replaceWith(ret);
					});
					return false;
				});
			});		
			</script>';
			
			$return = $pre.$return.$post;
		}
		
		return $return;
	}
}