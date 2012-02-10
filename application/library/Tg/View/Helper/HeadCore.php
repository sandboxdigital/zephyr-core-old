<?php
/**
 * Zend Framework
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
 * @category   Zend
 * @package    Zend_View
 * @subpackage Helper
 * @copyright  Copyright (c) 2005-2009 Zend Technologies USA Inc. (http://www.zend.com)
 * @version    $Id: HeadScript.php 16971 2009-07-22 18:05:45Z mikaelkael $
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

/** Zend_View_Helper_Placeholder_Container_Standalone */
require_once 'Zend/View/Helper/Placeholder/Container/Standalone.php';

/**
 * Helper for setting and retrieving script elements for HTML head section
 *
 * @uses       Zend_View_Helper_Placeholder_Container_Standalone
 * @package    Zend_View
 * @subpackage Helper
 * @copyright  Copyright (c) 2005-2009 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Tg_View_Helper_HeadCore extends Zend_View_Helper_Placeholder_Container_Standalone
{
	
	var $_defaults = array (
		'includeJquery' => false,
		'includeJqueryUI' => true,
		'includeExtjs' => true,
		'forceInclude' => false,
		'forceReload' => false
	);
	
    public function __construct()
    {
        parent::__construct();
        $this->setSeparator(PHP_EOL);
    }

    /**
     * Retrieve string representation
     *
     * @param  string|int $indent
     * @return string
     */
    
    public function script ($src)
    {
    	return '<script type="text/javascript" src="'.$src.'"></script>'.PHP_EOL;
    }
    
    public function css ($src)
    {
    	return '<link href="'.$src.'" media="screen" rel="stylesheet" type="text/css" />'.PHP_EOL;
    }
    
    public function toString($options = array ())
    {
		$options = $options+$this->_defaults;
		
		$html ='';
    	
	    $currentUser = Tg_Auth::getAuthenticatedUser();
		
	    // if logged in show the CMS edit features
	    // TODO change this so that only shows features if user has content editing rights.
		if ($options['forceInclude'] || ($currentUser && $currentUser->isMemberOf('ADMIN'))) 
		{
			$pre = Tg_Site::getInstance()->_pathPrefix();

			//$prepend = $options['forceInclude']?'?v='.rand(1, 999999):'';
			$prepend = '?v=2';
			
			if ($options['includeJquery']) {
				$html .= $this->script($pre.'core/js/jquery.js'.$prepend);
			}
			
			if ($options['includeJqueryUI']) {
				$html .= $this->css($pre.'core/css/jquery-ui-theme/ui.all.css'.$prepend);
				$html .= $this->script($pre.'core/js/jquery-ui.js'.$prepend);
			}
					
			if ($options['includeExtjs']) {
				$html .= $this->css($pre.'core/extjs/css/ext-all-notheme.css'.$prepend);
				$html .= $this->css($pre.'core/extjs/css/xtheme-gray.css'.$prepend);
			
				$html .= $this->script($pre.'core/extjs/js/ext-base.js'.$prepend);
				$html .= $this->script($pre.'core/extjs/js/ext-all.js'.$prepend);
			}
					
			$html .= $this->css($pre.'core/css/cms.css'.$prepend);
			$html .= $this->css($pre.'core/css/ext-reset-undo.css'.$prepend);
			$html .= $this->css($pre.'core/js/valums/fileuploader.css'.$prepend);
				
			$html .= $this->script($pre.'core/js/jquery.inherit.js'.$prepend);
			$html .= $this->script($pre.'core/js/jquery.cookie.js'.$prepend);
			$html .= $this->script($pre.'core/js/jquery.contextmenu.r2.js'.$prepend);
			$html .= $this->script($pre.'core/js/tiny_mce/tiny_mce.js'.$prepend);
			$html .= $this->script($pre.'core/js/swfupload/swfupload.js'.$prepend);
			
			$html .= $this->script($pre.'core/js/tg/core.js'.$prepend);
			$html .= $this->script($pre.'core/js/tg/fileupload.js'.$prepend);
			$html .= $this->script($pre.'core/js/valums/fileuploader.js'.$prepend);
			$html .= $this->script($pre.'core/extjs/ext/PagingStore.js'.$prepend);
			$html .= $this->script($pre.'core/extjs/ext/Ext.ux.TinyMCE.js'.$prepend);
			$html .= $this->script($pre.'core/js/tinymce.js'.$prepend);
			$html .= $this->script($pre.'core/js/tg/core.php.js'.$prepend);
			$html .= $this->script($pre.'core/js/tg/cms.page.js'.$prepend);
			$html .= $this->script($pre.'core/js/tg/cms.form.js'.$prepend);
			$html .= $this->script($pre.'core/js/tg/cms.form.field.js'.$prepend);
			$html .= $this->script($pre.'core/js/tg/cms.form.field.block.js'.$prepend);
			$html .= $this->script($pre.'core/js/tg/cms.form.field.group.js'.$prepend);
			$html .= $this->script($pre.'core/js/tg/cms.form.field.groupoption.js'.$prepend);
			$html .= $this->script($pre.'core/js/tg/cms.form.field.text.js'.$prepend);
			$html .= $this->script($pre.'core/js/tg/cms.form.field.textarea.js'.$prepend);
			$html .= $this->script($pre.'core/js/tg/cms.form.field.file.js'.$prepend);
			$html .= $this->script($pre.'core/js/tg/cms.form.field.html.js'.$prepend);
			$html .= $this->script($pre.'core/js/tg/cms.form.field.date.js'.$prepend);
			$html .= $this->script($pre.'core/js/tg/cms.form.field.select.js'.$prepend);
			$html .= $this->script($pre.'core/js/tg/cms.form.field.custom.js'.$prepend);
			
			$html .= $this->script($pre.'core/js/tg/tg.js'.$prepend);
			$html .= $this->script($pre.'core/js/tg/tg.windows.js'.$prepend);
			$html .= $this->script($pre.'core/js/tg/tg.doublepanel.js'.$prepend);
			$html .= $this->script($pre.'core/js/tg/tg.doublepanel.js'.$prepend);
			$html .= $this->script($pre.'core/js/tg/tg.filefactory.js'.$prepend);
			$html .= $this->script($pre.'core/js/tg/tg.filemanager.js'.$prepend);
			
			$html .= $this->script($pre.'core/js/tg/tg.pagetree.js'.$prepend);
			$html .= $this->script($pre.'core/js/tg/tg.pagefactory.js'.$prepend);
			$html .= $this->script($pre.'core/js/tg/tg.contentpanel.js'.$prepend);
		}
		return $html;
    }   
    
    public function headCore ($options = array())
    {
    	return $this->toString ($options);
    }
}
