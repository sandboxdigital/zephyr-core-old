<?php
/**
 * Tg Site Factory Class 
 *
 * @copyright  Copyright (c) 2009 Thomas Garrood (http://www.garrood.com)
 *
 */
class Tg_Site_Factory {
	protected static $_instance = null;
	protected $_rootPage;
	protected $_PageTemplates;
	protected $_currentPage;
	protected $_pageRoles;
	protected $_layouts;
	protected $_config;
	private $_currentLanguage = 'en';
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
     * @return  Tg_Site_Factory $instance
     */
	public static function getInstance() {
		if(self::$_instance === null) {
			self::$_instance = new self();
		}
		return self::$_instance;
	}
	
	public function __construct() {
		$this->_PageTemplates = array ();
						
		$config = Zend_Registry::getInstance()->get('siteconfig');
		
		if (isset($config['site']))
			$this->_config  = $config['site']+$this->_defaultConfig;
		else
			$this->_config  = $this->_defaultConfig;
		
		$this->_config['multiLingualLanguage'] = $this->_config['multiLingualDefaultLanguage'];


		if ($this->_config['pathPrefix'] == '/')
		{
	        $pathPre = Zend_Controller_Front::getInstance()->getBaseUrl();
//			echo $pathPre.'1111';die;
			if (!empty($pathPre))
			{
				$this->_config['pathPrefix'] = $pathPre.'/';
			}
		}

		// changed to lazy loading ... 
		//$this->load ();
	}
	
	public function __get ($name)
	{
		if ($name == 'CurrentPage')
			return $this->getCurrentPage();
		elseif ($name == 'RootPage')
			return $this->getRootPage();
	}

	function load ($force = false) 
	{		
		// TODO - add code to get from cache
		if ($this->_rootPage && $force==false)
			return;
		
		$this->loadPageTemplates ();
		$this->loadPages ();
		$this->loadThemes ();
	}
	
	public function loadThemes () {
		$pt = new Tg_Site_Db_Themes();
		$rows = $pt->fetchAll(null,'name');
		if (!$rows)
			throw new Zend_Exception('CMS Error: No Page Themes');
					
		foreach ($rows as $row)
			$this->_layouts[$row->id] = $row;
	}
	
	public function loadPageTemplates () {
		$pt = new Tg_Site_Db_PageTemplates();
		$rows = $pt->fetchAll(null,'name');
		if (!$rows)
			throw new Zend_Exception('CMS Error: No Page Templates');
					
		foreach ($rows as $row)
			$this->_PageTemplates[$row->id] = $row;
	}
	
	public function loadPages () {
		$pages = new Tg_Site_Db_Pages ();
		$rows = $pages->fetchAll(null,'left');
		if (!$rows) {
			$data = array (
			'title'=>'ROOT',
			'left'=>'1',
			'right'=>'2',
			'locked'=>'1',
			'visible'=>'1',
			'templateId'=>'1'
			);
			$pages->insert($data);
			
			throw new Zend_Exception ('CMS Error: No Pages');		
		}
		
		$this->_rootPage = $rows->current();
		$this->_rootPage->initPage ($null, $this);
		$rows->next();
		$this->_rootPage->populatPages ($rows, $this);
	}
	
	public function setCurrentPage ($Page) {
		$this->_currentPage = $Page;	
	}
	
	public function setLanguage ($lang)
	{
		// TODO - check $lang is a valid language
		self::setConfigOption('multiLingualLanguage', $lang);
		
		$t = Zend_Registry::get('translator');
		if (isset($t))
		{
			$t->setLocale($lang);
		}
	}
	
	/**
	 * Return the sites Current Page
	 * 
	 * @return Tg_Site_Db_Page
	 */
	public function getCurrentPage () 
	{
		$this->load ();

		return $this->_currentPage;
	}
	/**
	 * Return the sites RootPage
	 * 
	 * @return Tg_Site_Db_Page
	 */
	public function getRootPage () 
	{
		$this->load ();
		
		return $this->_rootPage;
	}
	
	public static function getLoginPage ()
	{
		$inst = self::getInstance();
		$page = $inst->getPage($inst->_config['loginPage'], true);
		
		if ($page) {
			return $page;
		} else {
			// hmmm Zend_Exception doesn't seem to work will we're routing
			die ('Login page not found at url '.$inst->_config['loginPage'].'. Use site.loginPage to define');
		}
	}
	
	public static function getConfigOption ($name)
	{
		$inst = self::getInstance();
		if (isset($inst->_config[$name])) {
			return $inst->_config[$name];
		} else {
			return false;
		}
	}
	
	public static function setConfigOption ($name, $value)
	{
		$inst = self::getInstance();
		$inst->_config[$name] = $value;
	}
	
	public static function isMultiLingual ()
	{
		return self::getConfigOption ('multiLingual');
		
	}
	
	public static function language ($uppercase = false)
	{
		$lang = self::getConfigOption ('multiLingualLanguage');
		
		if ($uppercase)
			return strtoupper($lang);
		else
			return $lang;
	}
	
	public static function pathPrefix ($lang=null)
	{
		return self::getInstance()->_pathPrefix ($lang);
	}
	
	public function _pathPrefix ($lang=null)
	{
		if ($this->_config['multiLingual'])
		{
			$lang = $lang==null?$this->_config['multiLingualLanguage']:$lang;
			
			if (isset($this->_config['multiLingualIgnoreDefault'])  && $lang == $this->_config['multiLingualDefaultLanguage'])
				return $this->_config['pathPrefix'];
			else 
				return $this->_config['pathPrefix'].$lang.'/';
		} else
			return $this->_config['pathPrefix'];
	}
	
    /**
     * Returns a page based on a path
     * 
     * @param  string $path 
     * @param  bool $strict=false 
     * @return Tg_Site_Db_Page $page
     */
	function &getPage ($path, $strict=false) {
		$currentPage = $this->getRootPage();
		
		$path = trim ($path, '/');
		
		if ($path=='' || $path=='/')
			return $currentPage;
		$path = explode("/", $path);
		return $currentPage->getPage ($path, $strict);
	}
	
    /**
     * Returns a page based on a path
     * 
     * @param  string $path 
     * @param  string $strict=false 
     * @return Tg_Site_Db_Page
     */
	function getPageById ($id) {
		$currentPage = $this->getRootPage();
		
		if ($id == $currentPage->id)
			return $currentPage;
		
		return $currentPage->getPageById ($id);		
	}	
	
	function appendPage (array $values, Tg_Site_Db_Page $ParentPage) {
		
		return $ParentPage->appendPage ($values);
	}

	function deletePage ($Page) {
		$Page->delete ();
	}
	
    /**
     * Returns all templates
     * 
     * @return array $templates
     */
	function &getTemplates () 
	{
		$this->load ();
		
		return $this->_PageTemplates;
	}

	public static function getThemePath ($file = '')
	{
		$ins = self::getInstance();
		$pre = $ins->_pathPrefix();
        $page = $ins->getCurrentPage();
        if ($page)
		    $folder = $pre.'themes/'.$page->getTheme()->folder;
        else
		    $folder = $pre.'themes/default';

        if ($file)
            return $folder.'/'.$file;
        else
            return $folder;
	}

	public static function getCorePath ($file='')
	{
		$pre = self::getInstance()->_pathPrefix();
		return $pre.'core/'.$file;
	}
	
    /**
     * Returns all templates as an array
     * 
     * @return array $templates
     */
	public static function getTemplatesAsArray ($onlyTemplateIds = null) 
	{
		$inst = self::getInstance();
		$inst->load ();
		
		$templateArray = array ();
		foreach ($inst->_PageTemplates as $template)
		{
			if ($onlyTemplateIds == null)
				$templatesArray[] = $template->toObject ();
			else if (in_array($template->id, $onlyTemplateIds))
				$templatesArray[] = $template->toObject ();
		};
		
		return $templatesArray;
	}
	
    /**
     * Returns a template
     * 
     * @param  int $templateId 
     * @return Tg_Site_Db_PageTemplate &template
     */
	function &getTemplate ($templateId) 
	{
		$this->load ();
		
		//print_r($templateId);
		if (!isset($this->_PageTemplates[$templateId]))
		{
			return $this->_PageTemplates[array_shift(array_keys($this->_PageTemplates))];
		}
		return $this->_PageTemplates[$templateId];
	}
	
    /**
     * Returns all layouts
     * 
     * @return array $layouts
     */
	public static function getThemes () 
	{
		$inst = self::getInstance();
		$inst->load ();
		return $inst->_layouts;
	}
	
    /**
     * Returns all templates as an array
     * 
     * @return array $templates
     */
	public static function getThemesAsArray () 
	{
		$inst = self::getInstance();
		$inst->load ();
		
		$layoutsArray = array ();
		foreach ($inst->_layouts as $layout)
		{
			$layoutsArray[] = $layout->toObject ();
		};
		
		return $layoutsArray;
	}
	
    /**
     * Returns a layout
     * 
     * @param  int $id 
     * @return Tg_Site_Db_Theme $layout
     */
	public static function getTheme ($id) 
	{
		$inst = self::getInstance();
		
		$inst->load ();
		
		if (!isset($inst->_layouts[$id]))
			throw new Zend_Exception("Could not find layout id");
		return $inst->_layouts[$id];
	}
}
?>
