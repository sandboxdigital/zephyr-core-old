<?php

class Tg_Form_Element_SelectMulti extends Zend_Form_Element_Multi
{
    public $helper = 'formSelectMulti';
    
    /**
     * SelectMulti is an array of values by default
     * @var bool
     */
    protected $_isArray = true;
}