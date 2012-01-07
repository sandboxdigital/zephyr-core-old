<?php
/**
* Redirect to HTTPS
* @author Riki Risnandar
*/
class Tg_Controller_Helper_NonSslSwitch extends Zend_Controller_Action_Helper_Abstract
{
	public function direct()
	{
		if (isset($_SERVER['HTTPS'])) {
			$request    = $this->getRequest();
			$url        = 'http://'
			. $_SERVER['HTTP_HOST']
			. $request->getRequestUri();
			$redirector = Zend_Controller_Action_HelperBroker::getStaticHelper('redirector');
			$redirector->gotoUrl($url);
		}
	}
}