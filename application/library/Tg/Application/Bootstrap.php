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

class Tg_Application_Bootstrap extends Zend_Application_Bootstrap_Bootstrap
{
	/**
	 * Initialize module loader
	 * 
	 * @return $moduleLoader Zend_Application_Module_Autoloader
	 */

    protected function _initApplication()
    {
        $this->bootstrap('frontcontroller');
        $front = $this->getResource('frontcontroller');
        $front->addModuleDirectory(Zeph_Core::getPath('%PATH_APPLICATION%/modules'));
        $front->addModuleDirectory(Zeph_Core::getPath('%PATH_CORE_APPLICATION%/modules'));
    }

	protected function _initDisableMagicQuotes() 
    { 
    	if (get_magic_quotes_gpc()) { 
	         $in = array(&$_GET, &$_POST, &$_COOKIE); 
	         while (list($k,$v) = each($in)) { 
	             foreach ($v as $key => $val) { 
	                 if (!is_array($val)) { 
	                     $in[$k][$key] = stripslashes($val); 
	                     continue; 
	                 } 
	                 $in[] =& $in[$k][$key]; 
	             } 
	         } 
	         unset($in); 
    	}
    }
    
	/**
	 * Initialize the environment 
	 * 
	 * @return $true true
	 */ 
	protected function _initConstants()
	{
		//// Define application environment
		define('APPLICATION_ENV', $this->_options['constants']['environment']);
	}
	
	/**
	 * Initialize the site config
	 * 
	 * @return $config Zend_Config_Ini
	 */	
	protected function _initSiteConfig()
	{
		Zend_Registry::set('config', $this->getOptions());
		Zend_Registry::set('siteconfig', $this->getOptions());	
		
		return $this->getOptions();
	}
    
    
	/**
	 * Initialize the file cache 
	 * 
	 * @return $true true
	 */ 
	protected function _initCache()
	{
		$cacheDir = Zeph_Core::getPath('%PATH_STORAGE%/cache');

		$dir = realpath($cacheDir);

		$frontendOptions = array(
		    'automatic_serialization' => true
		    );
		
		$backendOptions  = array(
		    'cache_dir'                => $dir
		    );
		
		$cache = Zend_Cache::factory('Core',
			'File',
			$frontendOptions,
			$backendOptions);

		// Plugin Helper Cache
		$classFileIncCache = Zeph_Core::getPath('%PATH_APPLICATION%/data/pluginLoaderCache.php');
		if (file_exists($classFileIncCache)) {
		    include_once $classFileIncCache;
		}
		if (Tg_Config::get('enablePluginLoaderCache') == true) {
		    Zend_Loader_PluginLoader::setIncludeFileCache($classFileIncCache);
		}
		
		// TODO - implement this:http://www.brandonsavage.net/caching-for-efficiency-with-zend-framework/
			
       return $cache;
	}
	
	/**
	 * Initialize the database 
	 * 
	 * @return $config Zend_Config_Ini
	 */ 
	protected function _initDbExtras()
	{
		$this->bootstrap('db'); // make sure we've loaded the db resource
		$db = $this->getResource('db');
		$db->setFetchMode(Zend_Db::FETCH_OBJ);
		Zend_Registry::set('db', $db);
		
		if (isset($this->_options['db'])) {
			if ((isset($this->_options['db']['profiler'])) && $this->_options['db']['profiler']==true) {
				$profiler = new Zend_Db_Profiler_Firebug('Database Queries');
			    $db->setProfiler($profiler);
			    $db->getProfiler()->setEnabled(true);   
			}
		    
		    if (isset($this->_options['db']['metadataCache'])) {
				if ((isset($this->_options['db']['metadataCache']['apc'])) && $this->_options['db']['metadataCache']['apc']==true) {
				    $cache = Zend_Cache::factory('Core', 'Apc', array('automatic_serialization' => true), array());
				    Zend_Db_Table_Abstract::setDefaultMetadataCache($cache);
				} elseif ((isset($this->_options['db']['metadataCache']['file'])) && $this->_options['db']['metadataCache']['file']==true) {
					$this->bootstrap('cache');
					$cache = $this->getResource('cache');
					
					//$cache = Zend_Cache::factory('Core', 'File', $options, array());
				    Zend_Db_Table_Abstract::setDefaultMetadataCache($cache);
			    }
		    }
		}
	}
	
	
	/**
	 * Initialize the locale for the site
	 * 
	 * @return $locale Zend_Locale
	 */	
	protected function _initLocale()
	{
		// set default timezone
		date_default_timezone_set ($this->_options['timezone']);
				
		$localeIn = isset($this->_options['locale'])?$this->_options['locale']:'en_US';
		
		// set locale
		$locale = new Zend_Locale($localeIn);
		Zend_Locale::setDefault($localeIn);
		Zend_Registry::set('Zend_Locale', $locale);	
		
		// create cache
		$this->bootstrap('cache');
		$cache = $this->getResource('cache');
		
		Zend_Locale_Data::setCache($cache);	

		return $locale;
	}

    protected function _initTranslations()
    {
    	// default form translator ... overrides ugly form error messages
    	$translator = new Zend_Translate('array', $this->_options['translation']);
    	Zend_Form::setDefaultTranslator($translator);
    	Zend_Registry::set('Zend_Translate', $translator);
    }

    protected function _initSession()
    {

	    $this->_options['session']['save_path'] = Zeph_Core::getPath($this->_options['session']['save_path']);
        if (!is_dir($this->_options['session']['save_path']))
        {
            echo 'Session directory doesn\'t exist <br/>';
            echo $this->_options['session']['save_path'];
            die;
        }
   		Zend_Session::setOptions($this->_options['session']);
    	
        return true;        
    }
		
	/**
	 * Initialize the logger
	 * 
	 * @return $logger
	 */	
	protected function _initLogger ()
	{
	}
	
	/**
	 * Initialize the view
	 * 
	 * @return $view
	 */	
    protected function _initView () 
    { 
    	$view = new Tg_View();
        $view->doctype('XHTML1_TRANSITIONAL'); 
//        $view->addHelperPath(APPLICATION_PATH . '/helpers');
        $view->headMeta()->appendHttpEquiv('Content-Type', 'text/html;charset=utf-8'); 
        $view->headTitle()->setSeparator(' - ');

        // get our current (logged in) user
        try {
        	$user = Tg_Auth::getAuthenticatedUser();
        	
	        if ($user) {
	            $view->currentUser = $user;
	        }
        } catch (Exception $e)
        {
        }

        $viewRenderer = Zend_Controller_Action_HelperBroker::getStaticHelper('ViewRenderer');
        $viewRenderer->setView($view);
        
        return $view;
    }
    
    protected function _initMVC()
    {
    	if (isset($this->_options['routes'])) {
    		if ((isset($this->_options['routes']['site'])) && $this->_options['routes']['site'] == true) {
				$this->bootstrap('FrontController'); // make sure we've loaded the db resource
				$frontController = $this->getResource('frontcontroller');

				$router = $frontController->getRouter(); // returns a rewrite router by default
				
				$options = array ('ignore' => array ('js', 'css', 'images',  'file', 'error', 'core'));
				$route = new Tg_Site_Route ($options);
				$router->addRoute ('site', $route);
    		}
    	}
    }
}

