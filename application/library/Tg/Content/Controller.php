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
 * Tg_Content_Controller 
 */

class Tg_Content_Controller extends Tg_Site_Controller
{
	protected $_showPageBar = true;
	protected $_includeCoreFiles = false;
	protected $_loadContent = true;
	protected $_content;
	
    public function init() {
    	parent::init ();
	    
    	if ($this->_loadContent)
	    	$this->_content = $this->view->content = $this->_page->getContent();
		
	    // if logged in show the CMS edit features
	    // TODO change this so that only shows features if user has content editing rights.
		if ($this->_currentUser) {
			if ($this->_includeCoreFiles || $this->_currentUser->isMemberOf('ADMIN')) {
				
		    	$session = new Zend_Session_Namespace('Tg_Content');
		    	$editMode = isset($session->editMode)?$session->editMode:'false';    	
				
		    	if ($this->_showPageBar)
		    		$this->view->headScript()->appendScript("$.cmsPanel.definePage(".$this->_page->toJson('read').",".$editMode.")");
			}
		}
	}
}
