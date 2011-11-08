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

class Tg_Form_Decorator_Description extends Zend_Form_Decorator_Description
{
	
	public function __construct($options = null)
	{
		parent::__construct($options);
	}

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
	
    public function render($content)
    {
        $element = $this->getElement();
        $view    = $element->getView();
        if (null === $view) {
            return $content;
        }

        $description = $element->getDescription();
        $description = trim($description);

        if (!empty($description) && (null !== ($translator = $element->getTranslator()))) {
            $description = $translator->translate($description);
        }

        if (empty($description)) {
            return $content;
        }

        $separator = $this->getSeparator();
        $placement = $this->getPlacement();
        $tag       = $this->getTag();
        $class     = $this->getClass();
        $escape    = $this->getEscape();

        $options   = $this->getOptions();

        $options['class'] = $class;
        
        if ($escape) {
            $description = $view->escape($description);
        }

        if (!empty($tag)) {
            require_once 'Zend/Form/Decorator/HtmlTag.php';
            $options['tag'] = $tag;
            $decorator = new Zend_Form_Decorator_HtmlTag($options);
            $description = $decorator->render($description);
        }

        switch ($placement) {
            case self::PREPEND:
                return $description . $separator . $content;
            case self::APPEND:
            default:
                return $content . $separator . $description;
        }
    }
}
