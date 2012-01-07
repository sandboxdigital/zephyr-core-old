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

class CmsController extends Tg_Content_Controller
{
	static $_contentFile = array (
		'index'=>'cms.xml',
		'twocol'=>'cms_twocol.xml'
		);
	static $_visible = true;
	
    public function indexAction()
    {
    	
    }
    
    public function twocolAction()
    {
    		
    }
}

