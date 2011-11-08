<?php

class Tg_Form_Element_TextMulti extends Zend_Form_Element_Multi
{
    public $helper = 'formTextMulti';
    
    /**
     * SelectMulti is an array of values by default
     * @var bool
     */
    protected $_isArray = true;
}