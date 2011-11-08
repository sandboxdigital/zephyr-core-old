<?php
/**
 * Tg Site Router Class 
 *
 * @copyright  Copyright (c) 2009 Thomas Garrood (http://www.garrood.com)
 *
 */
class Tg_Site_Route extends Zend_Controller_Router_Route_Abstract
{
    /**
     * URI delimiter
     */
    const URI_DELIMITER = '/';

    /**
     * Default values for the route (ie. module, controller, action, params)
     */
    protected $_defaults = array();
    protected $_values = array();
    protected $_params = array();
    protected $_path = "";
    protected $_moduleValid = false;
    protected $_keysSet     = false;

    protected $_moduleKey     = 'module';
    protected $_controllerKey = 'controller';
    protected $_actionKey     = 'action';

    /**
     * @var Zend_Controller_Dispatcher_Interface
     */
    protected $_dispatcher;

    /**
     * @var Zend_Controller_Request_Abstract
     */
    protected $_request;

    public function getVersion() {
        return 1;
    }
    
    /**
     * Instantiates route based on passed Zend_Config structure
     *
     * @param Zend_Config $config Configuration object
     */
    public static function getInstance(Zend_Config $config)
    {
        $defs = ($config->defaults instanceof Zend_Config) ? $config->defaults->toArray() : array();
        return new self($config->route, $defs);
    }

    /**
     * Constructor
     *
     * @param array $defaults Defaults for map variables with keys as variable names
     * @param Zend_Controller_Dispatcher_Interface $dispatcher Dispatcher object
     * @param Zend_Controller_Request_Abstract $request Request object
     */
    public function __construct(array $defaults = array(),
                Zend_Controller_Dispatcher_Interface $dispatcher = null,
                Zend_Controller_Request_Abstract $request = null)
    {
      $this->_defaults = $defaults;

        if (isset($request)) {
            $this->_request = $request;
        }

        if (isset($dispatcher)) {
            $this->_dispatcher = $dispatcher;
        }
    }

    /**
     * Matches a user submitted path with a previously defined route.
     * Assigns and returns an array of defaults on a successful match.
     *
     * @param string $path Path used to match against this routing map
     * @return array|false An array of assigned values or a false on a mismatch
     */
    public function match($path)
    {
    	$originalPath = $path;
		$this->_values = array ();
		$this->_params = array ();
		
		$ignore = isset($this->_defaults['ignore'])?$this->_defaults['ignore']:array();
    	
		try {
    		$Pm = Tg_Site::getInstance ();
    		$Page = $Pm->getRootPage();
		} catch (Exception $e) 
		{
			echo $e->getMessage();
			die;
		}
    	 // dispatch to root page by default
		$this->_values['controller'] = $Page->getTemplate()->controller;
		$this->_values['module'] = $Page->getTemplate()->module;
		$this->_values['action'] = 'index';
    	
        
        if ($path != '') {        	
			// remove prefix - if there is one        	
			$prefix = Tg_Site::getConfigOption('pathPrefix');
	        $path = substr($path,strlen($prefix));			
	        $path = trim($path, self::URI_DELIMITER);
            
	        // conver to array
	        $pathArray = explode(self::URI_DELIMITER, $path);
	        
			if (Tg_Site::getConfigOption('multiLingual'))
			{
				// if we're multilingual determine if the first path segment is 
				// our language identifier
				// Note site is designed to resolve to default language if no 
				// language identifier so (/admin/ is the same as /en/admin/)
				$lang = Tg_Site::getConfigOption('multiLingualDefaultLanguage');
				if (strlen($pathArray[0])==2) 
				{
					$languages = Tg_Site::getConfigOption('multiLingualLanguages');
					if (in_array($pathArray[0], $languages)) 
					{
						$lang = $pathArray[0];
						array_shift($pathArray); 
					}
				}
				Tg_Site::setLanguage($lang);
			}
			
        	if (count ($pathArray)>0) 
        	{
        		
	            if (in_array($pathArray[0], $ignore)) 
	            {
	            	// it's in our ignore list
	            	return false;            	
	            } else {
					// not in ignore list so redirect everything else to Site page
					
	//            	if (Tg_Site::isMultiLingual()) {
	//            		die;
	//            	}
	            	
					// loop through pages to find the leaf page to determine controller
					while ((count($pathArray)) && array_key_exists($pathArray[0], $Page->Pages)) {
						$Page = $Page->Pages[$pathArray[0]];
						array_shift($pathArray); // move to next
					}
			    	$this->_values['controller'] = $Page->getTemplate()->controller;
					$this->_values['module'] = $Page->getTemplate()->module;
					
					
					// determine action
					if ($Page->action != null) {
						$this->_values['action'] = $Page->action;
					} else {
			            if (count($pathArray) && !empty($pathArray[0])) {
			                $this->_values['action'] = array_shift($pathArray);
			            }
	            	}
			    	
			    	// determine params
		            if ($numSegs = count($pathArray)) {
		                for ($i = 0; $i < $numSegs; $i = $i + 2) {
		                    $key = urldecode($pathArray[$i]);
		                    $val = isset($pathArray[$i + 1]) ? urldecode($pathArray[$i + 1]) : null;
		                    $this->_params[$key] = $val;
		                }
		            }
		            
	            }	
			} 
        }
			
		if ($Page == $Pm->getRootPage()) {
			// hmmmm still on the rootpage - if there is an action then let's presume we're trying to route to a static controller not the homepage
			// TODO - change so the below works - it tests the rootpage controller to see 
			// if it has the required action if not routes to static controller
			// it's almost there
			
//			echo $this->_values['controller'];
//			$front =  Zend_Controller_Front::getInstance();
//			$dispatcher = $front->getDispatcher();
//			$controller = $dispatcher->formatControllerName ($this->_values['controller']);
//			
//			$default = $front->getDefaultModule();
//        	$dir = $front->getControllerDirectory($default);
//        	echo $dir;
//			
//			$dispatcher->setControllerDirectory($dir);
//			$cx = $dispatcher->getControllerClass (new Zend_Controller_Request_Http($this->_values));
//			echo $cx;
//			
//			$c = $dispatcher->loadClass ($controller);
//			$c = new $controller();
//			//var_dump(method_exists($controller, $this->_values['action'].'Action'));
//			echo $this->_values['action'];
//			die;
		
			if ($this->_values['action'] != 'index')
			{
				if (!Tg_Config::get('site.route404ToHome'))
					return false;	
//				else 
//				{
//			        dump ($this->_values);
//			        dump ($pathArray);
//			        die;
//				} 			
			}
		}
		
		// check if our logged in user has access to this resource 
		// possibly this should be in a FrontController Plugin
		$user = Tg_Auth::getAuthenticatedUser();
		$allowed = Tg_Site_Acl::isUserAllowed($user, $Page->path);
			
		if (!$allowed) 
		{
			// redirect to login controller	
			// should this use FrontController->dispatch instead?
		
	    	$LoginPage = Tg_Site::getLoginPage ();
	    	
	    	if ($LoginPage->url != $Page->url)
	    	{
	    		$Page = $LoginPage;
		    	
		    	if (Tg_Site::getConfigOption('redirectToLogin'))
		    	{
		    		$red = new Zend_Controller_Action_Helper_Redirector ();
		    		$red->gotoUrl($Page->url.'?redirectUrl='.$originalPath.'&message=Authentication%20required');
		    	} else {
				    $this->_values['controller'] = $Page->getTemplate()->controller;
					$this->_values['module'] = $Page->getTemplate()->module;
					$this->_values['action'] = $Page->action;
			    	$this->_params = array (
			    		'redirectUrl'	=> $originalPath,
			    		'message'		=>'Authentication required'
			    	);
		    	}
	    	}
		}
		
		$Pm->setCurrentPage ($Page);
	    
	    $this->_path = $Page->path;
		
        return $this->_values+$this->_params;
    }

	/**
     * Assembles user submitted parameters forming a URL path defined by this route
     *
     * @param array $data An array of variable and value pairs used as parameters
     * @param bool $reset Weither to reset the current params
     * @return string Route path with user submitted parameters
     */
 public function assemble($data = array(), $reset = false, $encode = true)
    {
    	
        foreach ($data as $key => $value) {
            if ($value !== null) {
                $this->_params[$key] = $value;
            } elseif (isset($this->_params[$key])) {
                unset($this->_params[$key]);
            }
        }
        
		$Page = Tg_Site::getInstance()->getCurrentPage ();
	    
		if (isset($this->_params['lang'])) {
    	    $url = $Page->getUrl ($this->_params['lang']);
	        unset ($this->_params['lang']);
		} else
	        $url = $Page->getUrl ();

       	$url = rtrim($url, self::URI_DELIMITER);
//
//       	dump ($url);
//        dump ($this->_values);
//        dump ($this->_params);
//        dump ($Page->action);
//        die;
       		
        // add the action
       
    	if (isset($this->_params['action'])) {
//    		echo $this->_params['action'];
       		$action = $this->_params['action'];
	        if (!empty($url) || $action !== $this->_defaults[$this->_actionKey]) {
	            if ($encode) 
					$action = urlencode($action);
	            $url .= '/'.$action;
	        }
	        unset ($this->_params['action']);
        } else if (empty($Page->action) && $this->_values['action']) {
	    	$url .= '/'.$this->_values['action'];
        } else if ($Page->action) {
    		// add to URL is not already added
//	    	$url .= '/'.$Page->action;
        } else {
	    	$url .= '/index';
        }
        
        foreach ($this->_params as $key => $value) {
            if (is_array($value)) {
                foreach ($value as $arrayValue) {
                    if ($encode) $arrayValue = urlencode($arrayValue);
                    $url .= '/' . $key;
                    $url .= '/' . $arrayValue;
                }
            } else {
                if ($encode) $value = urlencode($value);
                $url .= '/' . $key;
                $url .= '/' . $value;
            }
        }
        
        
        
        
        return ltrim($url, self::URI_DELIMITER);
    }

    /**
     * Return a single parameter of route's defaults
     *
     * @param string $name Array key of the parameter
     * @return string Previously set default
     */
    public function getDefault($name) {
        if (isset($this->_defaults[$name])) {
            return $this->_defaults[$name];
        }
        return null;
    }

    /**
     * Return an array of defaults
     *
     * @return array Route defaults
     */
    public function getDefaults() {
        return $this->_defaults;
    }

}
