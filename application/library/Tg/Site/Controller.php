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

/**
 * Tg Site
 */


class Tg_Site_Controller extends Zend_Controller_Action
{
	/**
	 * @var Tg_User_Db_User
	 */
	protected $_currentUser = null;

	/**
	 * @var Tg_Site_Db_Page
	 */
	protected $_page = null;
	    
    public function init() 
    {   	
		if ($user = Tg_Auth::getAuthenticatedUser())
			$this->_currentUser = $user;
		
    	$Pm = Tg_Site::getInstance ();
    	$this->_page = $Pm->getCurrentPage ();
    	$this->view->page = $Pm->getCurrentPage ();
    	
    	
    	if ($this->_page) {
			$this->view->headTitle($this->_page->metaTitle);
			
			if ($this->_page->metaDescription != '') {
				$this->view->headMeta($this->_page->metaDescription, 'description');
			}
				
			if (isset($this->_page->metaKeywords))
				$this->view->headMeta($this->_page->metaKeywords, 'keywords');
    	}

        if ($this->_page)
        {
        	$this->view->addScriptPath(Zeph_Core::getPath('%PATH_PUBLIC%/themes/'.$this->_page->getTheme()->folder.'/views/'));
            $this->view->addScriptPath(Zeph_Core::getPath('%PATH_PUBLIC%/themes/'.$this->_page->getTheme()->folder.'/views/layouts'));
        } else
        {
            $page = $Pm->getRootPage();
            $this->view->addScriptPath(Zeph_Core::getPath('%PATH_PUBLIC%/themes/'.$page->getTheme()->folder.'/views/'));
            $this->view->addScriptPath(Zeph_Core::getPath('%PATH_PUBLIC%/themes/'.$page->getTheme()->folder.'/views/layouts'));
        }


		Zend_Controller_Action_HelperBroker::addPrefix('Tg_Controller_Helper');
		$this->view->addHelperPath('Tg/View/Helper', 'Tg_View_Helper');
		
    	if ($this->isAjax ())
    	{
			$this->_helper->layout->disableLayout();
			$this->view->isAjax = true;
    	} else 
    		$this->view->isAjax = false;
	}


    public function setLayout ($layoutFile)
    {
        $layout = Zend_Layout::getMvcInstance();
        $layout->setLayout($layoutFile);
    }


    // TODO - move to Action Helper

	public function isAjax ()
	{
		return $this->_request->isXmlHttpRequest();
	}

    protected $_isMobileChecked = false;
    protected $_isMobileR = false;
    protected $_isMobileForceMobile = false;

    public function isMobile ($forceRecheck=false)
    {
        if ($this->_isMobileForceMobile)
            return true;

        if ($this->_isMobileChecked && $forceRecheck == false)
            return $this->_isMobileR;

        $_agents = array(
            'mobile'    => array('ipad'), // iPad contains mobile in AGENT but we don't consider it mobile
            'webos'        => false
            );

        $uAgent = $this->_request->HTTP_USER_AGENT;
        foreach ($_agents as $agent => $negation) {
            if (stripos($uAgent, $agent) !== false) {
                if (is_array($negation)) {
                   foreach ($negation as $neg) {
                       if (stripos($uAgent, $neg) !== false) {
                           $this->_isMobileChecked = true;
                           $this->_isMobileR = false;
                           return false;
                       }
                   }
                }
                $this->_isMobileChecked = true;
                $this->_isMobileR = true;
                return true;
            }
        }
        $this->_isMobileChecked = true;
        $this->_isMobileR = false;
        return false;
    }
}
