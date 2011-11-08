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
 * Tg_Content_Block_Controller 
 */

class Tg_Content_Block_Controller
{
//	protected $_showPageBar = true;
//	protected $_includeCoreFiles = false;
//	protected $_content;
	
    public function init() {
//    	parent::init ();
//    	
//	    $this->_content = $this->view->content = Tg_Content::getPageContent('SitePage'.$this->_page->id)->content();
//		
//	    // if logged in show the CMS edit features
//	    // TODO change this so that only shows features if user has content editing rights.
//		if ($this->_currentUser) {
//			if ($this->_includeCoreFiles || $this->_currentUser->isMemberOf('ADMIN')) {
//				if (Tg_Site::getConfigOption('includeJquery')) {
//					$this->view->headLink()->appendStylesheet('/core/css/jquery-ui-theme/ui.all.css');
//					$this->view->headScript()->appendFile('/core/js/jquery.js');
//					$this->view->headScript()->appendFile('/core/js/jquery-ui.js');
//				}
//				
//				$this->view->headLink()->appendStylesheet('/core/extjs/css/ext-all-notheme.css');
//				$this->view->headLink()->appendStylesheet('/core/extjs/css/xtheme-gray.css');
//				
//				$this->view->headLink()->appendStylesheet('/core/css/cms.css');
//				$this->view->headLink()->appendStylesheet('/core/css/ext-reset-undo.css');
//				$this->view->headLink()->appendStylesheet('/core/js/valums/fileuploader.css');
//				
//				$this->view->headScript()->appendFile('/core/extjs/js/ext-base.js');
//				$this->view->headScript()->appendFile('/core/extjs/js/ext-all.js');
//								
//					
//				$this->view->headScript()->appendFile('/core/js/jquery.inherit.js');
//				$this->view->headScript()->appendFile('/core/js/jquery.cookie.js');
//				$this->view->headScript()->appendFile('/core/js/jquery.contextmenu.r2.js');
//				$this->view->headScript()->appendFile('/core/js/tiny_mce/tiny_mce.js');
//				$this->view->headScript()->appendFile('/core/js/swfupload/swfupload.js');
//				
//				$this->view->headScript()->appendFile('/core/js/tg/core.js');
//				$this->view->headScript()->appendFile('/core/js/tg/fileupload.js');
//				$this->view->headScript()->appendFile('/core/js/valums/fileuploader.js');
//				$this->view->headScript()->appendFile('/core/extjs/ext/Ext.ux.TinyMCE.js');
//				$this->view->headScript()->appendFile('/core/js/tinymce.js');
//				$this->view->headScript()->appendFile('/core/js/tg/core.php.js');
//				$this->view->headScript()->appendFile('/core/js/tg/cms.page.js');
//				$this->view->headScript()->appendFile('/core/js/tg/cms.form.js');						
//				$this->view->headScript()->appendFile('/core/js/tg/cms.form.field.js');
//				$this->view->headScript()->appendFile('/core/js/tg/cms.form.field.block.js');
//				$this->view->headScript()->appendFile('/core/js/tg/cms.form.field.group.js');
//				$this->view->headScript()->appendFile('/core/js/tg/cms.form.field.groupoption.js');
//				$this->view->headScript()->appendFile('/core/js/tg/cms.form.field.text.js');
//				$this->view->headScript()->appendFile('/core/js/tg/cms.form.field.textarea.js');
//				$this->view->headScript()->appendFile('/core/js/tg/cms.form.field.file.js');
//				$this->view->headScript()->appendFile('/core/js/tg/cms.form.field.html.js');
//				$this->view->headScript()->appendFile('/core/js/tg/cms.form.field.date.js');
//				$this->view->headScript()->appendFile('/core/js/tg/cms.form.field.select.js');
//				
//				$this->view->headScript()->appendFile('/core/js/tg/tg.windows.js');
//				$this->view->headScript()->appendFile('/core/js/tg/tg.doublepanel.js');
//				$this->view->headScript()->appendFile('/core/js/tg/tg.filefactory.js');
//				$this->view->headScript()->appendFile('/core/js/tg/tg.filemanager.js');
//
//				
//		    	$session = new Zend_Session_Namespace('Tg_Content');
//		    	$editMode = isset($session->editMode)?$session->editMode:'false';    	
//				
//		    	if ($this->_showPageBar)
//		    		$this->view->headScript()->appendScript("$.cmsPanel.definePage(".$this->_page->toJson('read').",".$editMode.")");
//			}
    }
}
