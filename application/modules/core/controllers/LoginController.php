<?php

class LoginController extends Tg_Site_Controller
{
	public function init() 
	{
        parent::init();
		
		if ($this->_request->isXmlHttpRequest())
			$this->_helper->layout->setLayout('ajax');
    }

	// display and process login form
    public function indexAction() 
    {
    	$redirectUrl = $this->_getParam('redirectUrl');
    	$message = $this->_getParam('message');
    	
		$form = new Tg_User_Form_Login(null, $redirectUrl);
		
		if($this->_request->isPost()) {
			if($form->isValid($this->_request->getPost())) {
				
				$formValues = $form->getValues();
				
				$auth = Tg_Auth::login($formValues);
				if ($auth) {
					// TODO - get default redirect url from config
					if (empty($redirectUrl))
						$redirectUrl = '/admin';
					
					if ($this->_request->isXmlHttpRequest()) {
						$this->view->redirectUrl = $redirectUrl;
						$this->render('redirect');
					} else {
						$this->_redirect($redirectUrl);
					}
				}
				else {
					$this->view->message	 = 'Invalid Email Address or Password provided';
				}
			}
		} else {
			$this->view->message = $message;
		}
		
		$this->view->loginForm = $form;
    }

	// display and process password reset form
    public function forgotAction() {
		$form = new Tg_User_Form_ForgottenPassword ();
		
		$this->view->form = $form;
		
		if($this->_request->isPost()) {
			if($form->isValid ($this->_request->getPost())) {
				$user = Tg_User::getUserByEmail($form->email->getValue());
				
				if ($user) {
					$user->resetPassword ();
					$this->view->messageMessage = "Your new password has been sent to your registration e-mail";
					$this->view->form = null;
				} else {
					$this->view->messageMessage = "Email address not found";
				}
			}
		}
    }

	// clear auth session
    function logoutAction() {
		Tg_Auth::logout();
        $this->_helper->redirector->gotoUrl('/');
    }

}