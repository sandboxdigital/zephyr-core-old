<?php
class Tgx_Affiliate_ControllerPlugin extends Zend_Controller_Plugin_Abstract
{
    public function preDispatch(Zend_Controller_Request_Abstract $request)
    {
        if ($code = $request->getParam('affCode'))
        {
			$affiliate = Tgx_Affiliate::getAffiliateByCode($code);
			if ($affiliate) {
				// save as a cookie on the computer - this cookie should be retrieved 
				// when affiliate customer makes a purchase
				Tgx_Affiliate::setCookie ($affiliate);
			}
		}
    }
}