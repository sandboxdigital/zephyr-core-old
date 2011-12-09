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


class Tg_Site_Controller_Plugin_Layout extends Zend_Controller_Plugin_Abstract
{
    public function preDispatch(Zend_Controller_Request_Abstract $request)
    {
        // set the layout based on the current page(template)
		$layout = Zend_Layout::getMvcInstance();
    	$page = Tg_Site::getInstance()->getCurrentPage();
//
    	if ($page) {
            $theme = $page->getTheme();
            $layout->setLayoutPath(PUBLIC_PATH.'/themes/'.$theme->folder.'/views/layouts');
    	}
    }
}