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

class Core_Admin_ContentController extends Tg_Content_Controller
{
    public function init() {
    	$this->_showPageBar = false;
    	parent::init ();    	
    }
	
    public function indexAction() 
    {
		$this->view->headLink()->appendStylesheet('/core/css/admin/content.css');
		
		$this->view->headScript()->appendFile('/core/js/tg/tg.pagetree.js');
		$this->view->headScript()->appendFile('/core/js/tg/tg.pagefactory.js');
		$this->view->headScript()->appendFile('/core/js/tg/tg.contentpanel2.js');
		$this->view->headScript()->appendFile('/core/js/admin/content_index.js');
		
		$this->view->pageNodes = Tg_Site::getInstance()->getRootPage()->toJson('read') ;
		$this->view->layoutNodes = Zend_Json::encode (Tg_Site::getLayoutsAsArray());
		$this->view->templateNodes = Zend_Json::encode (Tg_Site::getTemplatesAsArray());
    }
    
    public function templatesAction ()
    {
		$this->view->headLink()->appendStylesheet('/core/css/admin/content.css');
		
		$this->view->headScript()->appendFile('/core/js/tg/tg.pagetree.js');
		$this->view->headScript()->appendFile('/core/js/tg/tg.pagefactory.js');
		$this->view->headScript()->appendFile('/core/js/tg/tg.contentpanel2.js');
		$this->view->headScript()->appendFile('/core/js/admin/content_templates.js');
		
		$this->view->templateNodes = Zend_Json::encode (Tg_Site::getTemplatesAsArray());
    }
        
    public function loadAction() 
    {
    	$response = new stdClass();
	    $response->success = true;
    	try {
    		$contentId = $this->_getParam('contentId');
    		// $contentTemplateId = $this->_getParam('contentTemplateId');
    		$contentVersion = $this->_getParam('version');
    		
    		if (strpos($contentId, 'SitePageTemplate')===0)
    		{
	    		$content = Tg_Content::getContent($contentId, $contentVersion);
	    		$form = $this->getFormFromSitePageTemplate($contentId); // TODO - change to use contentTemplateId
    		} else if (strpos($contentId, 'SitePage')===0)
    		{
	    		$content = Tg_Content::getPageContent($contentId, $contentVersion);
	    		$form = $this->getTemplateForm($contentId); // TODO - change to use contentTemplateId
    		} else 
    		{
    			
    		}
	    	
			$options = array (
				'url'=>'/admin/content/save2',
				'contentId'=>$content->name
			);
				    	
	    	$response->data = Tg_Content::getInstance()->getFormObject ($form, $content->data, $options);
			$response->data->versions = Tg_Content::getContentVersions ($contentId);
			$response->data->currentVersion = $content->version;
    	} catch (Exception $e) {
			$response->success = false;
			$response->error = $e->getMessage();
		}
		
		echo Zend_Json::encode ($response);
		die;
    }
    
    // wrapper to load content from site_page if it doesn't exist in content
    // TODO - should probably have a content_template table same as .Net framework
    public function getTemplateForm ($contentId)
    {	
    	$pageId = substr($contentId, 8); // SitePage
		$page = Tg_Site::getInstance()->getPageById($pageId);
		if (!$page)
			throw new Exception('Page not found '.$pageId);
			
		$template = $page->getTemplate();
		if (!empty($template->form))
    		return $template->form;
    	else
    		return  $template->controller.'.xml';
    }
    
    // wrapper to load content from site_page if it doesn't exist in content
    // TODO - should probably have a content_template table same as .Net framework
    public function getFormFromSitePageTemplate ($contentId)
    {	
    	$id = substr($contentId, 16); // SitePageTemplate
		$template = Tg_Site::getInstance()->getTemplate($id);
		if (!$template)
			throw new Exception('Page Template not found '.$id);
			
		if (!empty($template->form))
    		return $template->form;
    	else
    		return  $template->controller.'.xml';
    }
	
    public function save2Action() 
    {
    	Tg_Log::logToDb("save2: ".$this->_getParam('contentId')."\n".$_POST['cmsForm']);

    	$response = new stdClass();
	    $response->success = true;
    	try {
    		$contentId = $this->_getParam('contentId');
    		$bodytext = $_POST['cmsForm'];
    		
    		// Shouldn't need to do this as it's done in the Javascript
			//$bodytext = $this->stripInvalidXml($bodytext);
			$content = Tg_Content::saveContent ($contentId, $bodytext);
			
			$response->data = new stdClass();
			$response->data->versions = Tg_Content::getContentVersions ($contentId);
			$response->data->currentVersion = $content->version;
			
    	} catch (Exception $e) {
			$response->success = false;
			$response->error = $e->getMessage();
		}
		
    	Tg_Log::logToDb("save2 response: \n".Zend_Json::encode ($response));
		
		echo Zend_Json::encode ($response);
		die;
    } 
        
    // depreciated
    public function captureAction() 
    {
    	$hideSave = $this->_getParam('hideSave','false');
    	$pageId = $this->_getParam('pageId');
		$page = Tg_Site::getInstance()->getPageById($pageId);
		$template = $page->getTemplate();
		
		$options = array (
			'url'=>'/admin/content/save2',
			'pageId'=>$pageId,
			'contentId'=>'SitePage'.$pageId,
			'hideSave'=>$hideSave
		);
    	
    	$this->view->form = Tg_Content::getInstance()->getFormFromFile ($template->form, $page->dataXML, $options);
    }
        
    // depreciated
    public function capture2Action() 
    {
    	$response = new stdClass();
	    $response->success = true;
    	try {
	    	$contentId = $this->_getParam('contentId');
	    	$pageId = substr($contentId, 8); // SitePage
			$page = Tg_Site::getInstance()->getPageById($pageId);
			if (!$page)
				throw new Exception('Page not found '.$pageId);
				
			$template = $page->getTemplate();
			
			$options = array (
				'url'=>'/admin/content/save2',
				'pageId'=>$pageId,
				'contentId'=>$contentId
			);
	    	
	    	$response->data = Tg_Content::getInstance()->getFormObject ($template->form, $page->dataXML, $options);
    	} catch (Exception $e) {
			$response->success = false;
			$response->error = $e->getMessage();
		}
		
		echo Zend_Json::encode ($response);
		die;
    }
	
    // depreciated
    public function saveAction() 
    {
    	$pageId = $this->_getParam('pageId');
		$page = Tg_Site::getInstance()->getPageById($pageId);
		$template = $page->getTemplate();
		
    	$page->dataXML = '<?xml version="1.0"?>'.stripslashes($_POST['cmsForm']);
    	
    	$page->save();
    	
    	echo '{"success":true}';
		die;
    }  
    
    function stripInvalidXml($value)
	{
		// this first line of code is probably redundant - also doesn't seem to catch all non XML characters
		/* This will Convert the string to the requested character encoding and strip all of the none-utf-8 characters (http://uk3.php.net/manual/en/function.iconv.php */
		$value = iconv("UTF-8","UTF-8//IGNORE",$value); 
		
	    $ret = "";
	    $current;
	    if (empty($value)) 
	    {
	        return $ret;
	    }
	 
	    $length = strlen($value);
	    for ($i=0; $i < $length; $i++)
	    {
	        $current = ord($value{$i});
	        if (($current == 0x9) ||
	            ($current == 0xA) ||
	            ($current == 0xD) ||
	            (($current >= 0x20) && ($current <= 0xD7FF)) ||
	            (($current >= 0xE000) && ($current <= 0xFFFD)) ||
	            (($current >= 0x10000) && ($current <= 0x10FFFF)))
	        {
	            $ret .= chr($current);
	        }
	        else
	        {
	            $ret .= "?";
	        }
	    }
	    return $ret;
	}
}

?>