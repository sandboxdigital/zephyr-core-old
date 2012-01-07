<?php

class RegisterController extends Tg_Site_Controller
{
    public function indexAction() 
    {
    	$redirectUrl = $this->_getParam('redirectUrl');
    	
		$form = new Form_Register(array ('redirectUrl'=>$redirectUrl));

		if ($this->_request->isPost()) {
			if ($form->isValid($this->_request->getPost())) {
				$data = $form->getValues();
				
				Tg_User::register ($data);
				
		        if ($redirectUrl)
		        	$this->_redirect ($redirectUrl);
		        else
					$this->render ('thankyou');
			}						
		}
		
		$this->view->form = $form;
    }
}