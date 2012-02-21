<?php

class ErrorController extends Zend_Controller_Action
{
    public function errorAction()
    {
        $errors = $this->_getParam('error_handler');
	    $exception = $errors->exception;

	    dump($exception->getMessage());
	    dump($exception->getTraceAsString());
    }
}

