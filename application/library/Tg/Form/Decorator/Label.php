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

class Tg_Form_Decorator_Label extends Zend_Form_Decorator_Label
{
    /**
     * Get class with which to define label
     *
     * Appends 'error' to class, if there is an error in the form for the
     * associated element
     *
     * @return string
     */
	
    public function getClass()
    {
        $class = parent::getClass();

        $element = $this->getElement();

        if ($element->hasErrors()){
            if (!empty($class)){
                $class .= ' invalid';
            }else{
                $class = 'invalid';
            }
        }
       
        return $class;
    }


    /**
     * Render a label
     *
     * @param  string $content
     * @return string
     */
    public function render($content)
    {
        $this->setReqSuffix (' <span class="required">*</span>');
        
        $element = $this->getElement();
        $view    = $element->getView();
        if (null === $view) {
            return $content;
        }

        $label     = $this->getLabel();
        $separator = $this->getSeparator();
        $placement = $this->getPlacement();
        $tag       = $this->getTag();
        $id        = $this->getId();
        $class     = $this->getClass();
        $options   = $this->getOptions();
        
        $options['escape']=false;

        if (empty($label) && empty($tag)) {
            return $content;
        }

        if (!empty($label)) {
            $options['class'] = $class;
            $label = $view->formLabel($element->getFullyQualifiedName(), trim($label), $options);
        } else {
            $label = '&nbsp;';
        }
        
        if (null !== $tag) {
        	$options = array ();
        	$options['tag'] =  $tag;
        	$options['class'] = $class;
        	$options['id'] = $this->getElement()->getName() . '-label';
        	
            require_once 'Zend/Form/Decorator/HtmlTag.php';
            $decorator = new Zend_Form_Decorator_HtmlTag();
            $decorator->setOptions($options);

            $label = $decorator->render($label);
        }

        switch ($placement) {
            case self::APPEND:
                return $content . $separator . $label;
            case self::PREPEND:
                return $label . $separator . $content;
        }
    }


}
