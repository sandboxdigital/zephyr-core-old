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
 * Tg System
 */
class Tg_System
{
    protected static $_instance = null;

    private function __construct()  
    {

    }

    public static function getInstance()
    {
        if (null === self::$_instance) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }
    
    public static function getInstalledModules ()
    {
    	$modulesTable = new Tg_System_Db_Table_Modules ();
    	$modulesRowset = $modulesTable->fetchAll(null,'id');
    	
    	$modules = array ();
    	
    	foreach ($modulesRowset as $module) {
    		$modules[$module->path] = $module;
    	}
    	
    	return $modules;
    }
    
    public static function getInstalledModule ($id)
    {
    	$modules = new Tg_System_Db_Table_Moduless ();
    	return $modules->fetchRow($users->select()->where ('id=?',$id));
    
    }
    
    public static function getAvailableModules ()
    {
    	$installedModles = self::getInstalledModules();
    	
    	$modules = array ();
    	
    	$controllerDirs = Zend_Controller_Front::getInstance()->getDispatcher()->getControllerDirectory();

    	// ignore the first as it's just application/controllers
    	array_shift($controllerDirs);
    	
    	foreach ($controllerDirs as $controllerDir) {
    		//TODO read manifest from controller dir to check that it is a module
    		
    		$controllerDir = explode('/',$controllerDir);
    		
    		$path = $controllerDir[count($controllerDir)-2];
    		
    		if (!isset($installedModles[$path])) {
	    		$modules[$path] = array (
	    			'name'=>$path,
	    			'path'=>$path
	    		);
    		}
    	}
    	
    	return $modules;
    }
}