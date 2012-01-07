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
        	$this->view->addScriptPath(PUBLIC_PATH.'/themes/'.$this->_page->getTheme()->folder.'/views/');
            $this->view->addScriptPath(PUBLIC_PATH.'/themes/'.$this->_page->getTheme()->folder.'/views/layouts');
        } else
        {
            $page = $Pm->getRootPage();
            $this->view->addScriptPath(PUBLIC_PATH.'/themes/'.$page->getTheme()->folder.'/views/');
            $this->view->addScriptPath(PUBLIC_PATH.'/themes/'.$page->getTheme()->folder.'/views/layouts');
        }


		Zend_Controller_Action_HelperBroker::addPrefix('Tg_Controller_Helper');
		$this->view->addHelperPath('Tg/View/Helper', 'Tg_View_Helper');
		
    	if ($this->_request->isXmlHttpRequest()) 
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
}