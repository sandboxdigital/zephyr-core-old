<?php

class Tg_Form_Element_FileUploadMulti extends Zend_Form_Element_Multi
{
    public $helper = 'formFileUploadMulti';
    
    protected $_isArray = true;
}