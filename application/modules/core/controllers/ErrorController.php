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

class ErrorController extends Tg_Site_Controller
{
	static $_visible = false;
	
	public function init ()
	{
		parent::init ();
		
		// set up context so we can get errors in xml or json
        $contextSwitch = $this->_helper->getHelper('contextSwitch');
        $contextSwitch->addActionContext('error', 'xml')->initContext();
        $contextSwitch->addActionContext('error', 'json')->initContext();
	}
	
    public function errorAction()
    {
        $errors = $this->_getParam('error_handler');
        $config = Zend_Registry::get('config');
        $exception = $errors['exception'];
        $errorCode = 0;
                
        switch ($errors->type) { 
            case Zend_Controller_Plugin_ErrorHandler::EXCEPTION_NO_CONTROLLER:
            case Zend_Controller_Plugin_ErrorHandler::EXCEPTION_NO_ACTION:
        
                // 404 error -- controller or action not found
                $errorCode = 404;
                $this->getResponse()->setHttpResponseCode(404);
                $this->view->message = 'Page not found';
                break;
            default:
                // application error 
                $errorCode = 500;
                $this->getResponse()->setHttpResponseCode(500);
                $this->view->message = $exception->getMessage();
                break;
        }
        
        $this->view->exception = $errors->exception;
        $this->view->request   = $errors->request;
                
        if (Tg_Config::get('errors'))
        {
        	if ($errorCode != 404 || Tg_Config::get('errors.log404')) {
	        	if (is_array($config['errors']['email']))
	        	{		
		        	$email = new Tg_Mail();			
	        		try {
	        			$emails = $config['errors']['email'];
			        	$email->to = $emails[0];
			        	unset ($emails[0]);
			        	foreach ($emails as $cc)
		        			$email->addCc($cc);
		        		$email->subject = 'Site exception - ' .$exception->getCode().' - '.$exception->getMessage();
						$email->template = 'exception';
		        		$email->variables = array(
		        			'trace'=>'<pre>'.$exception->getTraceAsString().'</pre>'
		        			,'message'=>$exception->getMessage()
		        			,'server'=>'<pre>'.print_r($_SERVER,true).'</pre>'
		        			,'session'=>'<pre>'.print_r($_SESSION,true).'</pre>'
		        			,'request'=>'<pre>'.print_r($errors['request'],true).'</pre>'
		        		);
		        		$email->send();
	        		}catch (Exception $e) {
//	        			
//        					echo '<pre>'.$email->body.'</pre>';
//		        			die;
	        		}	        	
	        	}
        	}        
        }
        
    }

    public function insufficientPrivilegesAction ()
    {
		$this->view->message = 'Insufficient Privileges';
		$this->render('error');
    	
    }
}

