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

class Tg_Form_Decorator_FormErrors extends Zend_Form_Decorator_Abstract
{
	private $_translator;
	
    public function __construct($options = null)
    {
        parent::__construct($options);

        //setMarkupListStart('<ul class="form_errors">');
    }

    public function render($content)
    {
        $form = $this->getElement();
        if (!$form instanceof Zend_Form) {
            return $content;
        }

        $message = $this->getOption('message');
        if (empty($message)){
            $message = '';
        }
        
        // use the forms translator for the summary message
        $this->_translator = $form->getTranslator();
        
        if ($this->_translator !== null){
            $message = $this->_translator->translate($message);
        }

        $view = $form->getView();
        if (null === $view) {
            return $content;
        }

        $errors = $form->getMessages();
                
        if (empty($errors)) {
            return $content;
        }
                
        $markup = '<div class="form_errors_block">';
        if (!empty($message)){
            $markup .= '<p class="message">' . $message . '</p>';
        }
        $markup .= '<ul class="form_errors">';

        $markup .= $this->_renderErrors($form, $errors);

        $markup .= '</ul></div>';
        
        switch ($this->getPlacement()) {
            case self::APPEND:
                return $content . $this->getSeparator() . $markup;
            case self::PREPEND:
                return $markup . $this->getSeparator() . $content;
        }        

        return $content;  
    }
    
    private function _renderErrors ($form, $errors)
    {
        $view = $form->getView();
        
    	$markup = '';
    	foreach ($errors as $name => $list) {
            $element = $form->$name;
            
            if ($element instanceof Zend_Form) {
//            	dump ($list);
            	$markup .= $this->_renderErrors($element, $list);
            } else if ($element instanceof Zend_Form_Element) {
				
                $label = $element->getLabel();
                if (empty($label)) {
                    $label = $element->getName();
                }
                $label = trim($label);
                if (empty($label)) {
                    $label = '';
                }
                if (null !== ($this->_translator = $element->getTranslator())) {
                    $label = $this->_translator->translate($label);
                }
                
                $error_msg = '';
                foreach ($list as $key => $error) {
//            		dump ($key);
//            		dump ($error);
//           			$message = $this->_translator->translate($message);
                    $error_msg = $view->escape($error);
                    break; // just do the first error message for a field
                }
                
            	$error_msg = str_replace('%label%', '<span class="label">' . $label . '</span>', $error_msg);
                
                $markup .= '<li>'. $error_msg . '</li>';
            }
            else{
                if (is_string($list)){
                    $markup .= '<li>' . $list . '</li>';
                }
            }
        }
        return $markup;
    }
}
