<?php
/**
 * Tg Nav Factory Class
 *
 * @copyright  Copyright (c) 2009 Thomas Garrood (http://www.garrood.com)
 *
 */
class Tg_Nav_Factory {
	protected static $_instance = null;
	protected $_navs;
	protected $_config;
	private $_defaultConfig 	= array (
		'loginPage'						=>'/login',
		'includeJquery'					=>false,
		'multiLingual'					=>false,
		'multiLingualDefaultLanguage'	=>'en',
		'multiLingualLanguage		'	=>'en',
		'multiLingualLanguages'			=> array ('en','de','fr','es','it'),
		'pathPrefix'					=>'/'

	);
	
    /**
     * Get singleton instance
     * 
     * @return  Tg_Nav_Factory $instance
     */
	public static function getInstance() {
		if(self::$_instance === null) {
			self::$_instance = new self();
		}
		return self::$_instance;
	}
	
	public function __construct() {
	}

	function load ($force = false) 
	{		
		// TODO - add code to get from cache
		if ($this->_navs && $force==false)
			return;

		$this->loadNavs ();
	}
	
	public function loadNavs () {
        if (!$this->_navs) {
            $pages = new Tg_Nav_Db_Navs ();
            $rows = $pages->fetchAll(null);
            if (!$rows) {
                $data = array (
                    'name'=>'Main'
                );
                $pages->insert($data);
                $rows = $pages->fetchAll(null);
            }
            $this->_navs = $rows;
        }
	}

	/**
	 * Return the sites Current Page
	 * 
	 * @return array
	 */
	public static function getNavs ()
	{
        $inst = self::getInstance();
        $inst->load ();

		return $inst->_navs;
	}

    /**
     * Return the sites Current Page
     *
     * @return Tg_Nav_Db_Nav
     */
    public static function getNav ($navName)
    {
        $inst = self::getInstance();
        $inst->load ();

        foreach($inst->_navs as $nav){
            if ($navName == $nav->name)
            {
                return $nav;
            }
        }

        return null;
    }

	
    /**
     * Returns a page based on a path
     * 
     * @param  string $path 
     * @param  bool $strict=false 
     * @return Tg_Nav_Db_Navitem $page
     */
	function getNavitem ($path, $strict=false)
    {
		$currentNavitem = $this->getRootNavitem();
		
		$path = trim ($path, '/');
		
		if ($path=='' || $path=='/')
			return $currentNavitem;
		$path = explode("/", $path);
		return $currentNavitem->getNavitem ($path, $strict);
	}

    /**
     * @param $id
     * @return Tg_Nav_Db_Navitem
     */
    function getNavitemById ($id)
    {
        $this->load ();

        foreach($this->_navs as $nav) {
            $item = $nav->getNavitemById($id);
            if ($item) {
                return $item;
            }
        }
        return null;
    }

	function appendNavitem (array $values, Tg_Nav_Db_Navitem $ParentNavitem) {
		
		return $ParentNavitem->appendNavitem ($values);
	}

	function deleteNavitem ($Navitem) {
		$Navitem->delete ();
	}
}