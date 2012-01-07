<?php

class Core_Admin_SiteController extends Tg_Content_Controller
{
	protected static $_visible = false;

	
    public function init() {
    	$this->_showPageBar = false;
    	
    	parent::init ();    	
				
		$this->view->headLink()->appendStylesheet('/core/css/pm.css');

        $ajaxContext = $this->_helper->getHelper('ajaxContext');
        $ajaxContext->addActionContext('ajax-page-save', 'json')
                    ->initContext('json');
	}
    
	public function indexAction() 
    {
    	$this->pagesAction();
    	$this->render('pages');
    }

    public function pagesAction()
    {
		$this->view->headLink()->appendStylesheet('/core/css/admin/content.css');
		
		$this->view->headScript()->appendFile('/core/js/tg/tg.pagetree.js');
		$this->view->headScript()->appendFile('/core/js/tg/tg.pagefactory.js');
		//$this->view->headScript()->appendFile('/core/js/tg/tg.contentpanel.js');
		$this->view->headScript()->appendFile('/core/js/admin/site_page.js');
		
		$this->view->pageNodes = Tg_Site::getInstance()->getRootPage()->toJson('read') ;
		$this->view->themeNodes = Zend_Json::encode (Tg_Site::getThemesAsArray());
		$this->view->templateNodes = Zend_Json::encode (Tg_Site::getTemplatesAsArray());
    }

    public function pagesExportAction()
    {
		$this->_helper->layout->disableLayout();
		$this->_helper->viewRenderer->setNeverRender();
    	$t = new Tg_Site_Db_Pages();
    	$rows = $t->fetchAll($t->select()->order('left'));
    	$rows->exportToCsv();
    }

    public function pageAddAction ()
    {
		$Pm = Tg_Site::getInstance ();
		
		$Parent = $Pm->getPageById($this->_getParam ('parentId'));
		if (!$Parent)
			throw new Exception ("Parent not found");
					
		$data = array (
			'parentId'=>$Parent->id,
			'layoutId'=>$Parent->layoutId,
			'templateId'=>$Parent->templateId
			);
    	$Form = new Tg_Site_Form_Page (array('isAdd'=>true));
    	$Form->setAction('/admin/site/page-add');
    	if ($this->_request->isPost()) {
	        if ($Form->isValid($_POST)) {
				$Page = $Pm->appendPage ($Form->getValues (), $Parent);
    			$Form->setDefaults($Page->toArray());
    			
    			$this->view->Page = $Page;
				$this->view->updatePageList = true;
				$this->view->form = $Form;
				$this->render ('page-edit');
				return;
			}
    	} else
    		$Form->populate ($data);
    	
    	$this->view->Parent = $Parent;
		$this->view->form = $Form;
    }

    public function pageEditAction ()
    {    	
    	$Page = Tg_Site::getInstance ()->getPageById($this->_getParam ('id'));
		if (!$Page)
			throw new Exception ("Page not found");
			
    	$Form = new Tg_Site_Form_Page ($Page);
    	$Form->setAction('/admin/site/page-edit');
    	if ($this->_request->isPost()) {
	        if ($Form->isValid($_POST)) {
				$Page->update ($Form->getValues ());
				$this->view->message = "Updated";
				$this->view->updatePageList = true;
			}
    	} else
    		$Form->setDefaults($Page->toArray());
    		
		$this->view->Page = $Page;
		$this->view->form = $Form;
		$this->view->showSubPages = $this->_getParam ('showSubPages', 'true');
    }
    
    public function pageMetaAction() 
    {
		$this->view->headLink()->appendStylesheet('/core/css/admin/content.css');
		
		$this->view->headScript()->appendFile('/core/js/admin/content.js');
		$this->view->headScript()->appendFile('/core/js/tg/tg.windows.js');
		$this->view->headScript()->appendFile('/core/js/tg/tg.pagetree.js');
		$this->view->headScript()->appendFile('/core/js/tg/tg.pagefactory.js');
		
		$this->view->pageNodes = Tg_Site::getInstance()->getRootPage()->toJson('read') ;
		$this->view->layoutNodes = Zend_Json::encode (Tg_Site::getThemesAsArray());
		$this->view->templateNodes = Zend_Json::encode (Tg_Site::getTemplatesAsArray());
    }

    public function pageMetaEditAction ()
    {    	
    	$Page = Tg_Site::getInstance ()->getPageById($this->_getParam ('id'));
		if (!$Page)
			throw new Exception ("Page not found");
			
    	$Form = new Tg_Site_Form_PageMeta ($Page);
    	if ($this->_request->isPost()) {
	        if ($Form->isValid($_POST)) {
	        	$data = $Form->getValues ();
				$Page->metaTitle = $data['metaTitle'];
				$Page->metaDescription = $data['metaDescription'];
				$Page->metaKeywords = $data['metaKeywords'];
				$Page->save();
				$this->view->message = "Updated";
			}
    	} else {
			$data = $Page->toArray();
			
			if (empty($data['metaTitle']))
				$data['metaTitle'] = $data['title'];
			
    		$Form->setDefaults($data);
    	}
    		
		$this->view->Page = $Page;
		$this->view->form = $Form;
    }

    public function pageRolesAction ()
    {    	
    	$Page = Tg_Site::getInstance ()->getPageById($this->_getParam ('id'));
		if (!$Page)
			throw new Exception ("Page not found");
			
		$this->view->roles = Tg_User::getRoles();
		$this->view->pageRoles = $Page->getRoles();
		$this->view->page = $Page;
    }

    public function pageRoleAddAction ()
    {
    	Tg_Site_Acl::addRoleToPage($this->_getParam ('page'), $this->_getParam ('role'));
			
		$Page = Tg_Site::getInstance ()->getPageById($this->_getParam ('page'));
			
		$this->view->roles = Tg_User::getRoles();
		$this->view->pageRoles = $Page->getRoles();
		$this->view->page = $Page;
		
		$this->render ('page-roles');
    }

    public function pageRoleDeleteAction ()
    {
    	Tg_Site_Acl::deleteRoleFromPage($this->_getParam ('page'), $this->_getParam ('role'));
			
		$Page = Tg_Site::getInstance ()->getPageById($this->_getParam ('page'));
			
		$this->view->roles = Tg_User::getRoles();
		$this->view->pageRoles = $Page->getRoles();
		$this->view->page = $Page;
		
		$this->render ('page-roles');
    }

    public function pageEditPropertiesAction ()
    {
		if (!($Page = Tg_Site::getInstance ()->getPageById($this->_getParam ('id'))))
			throw new Exception ("Page not found");
			
    	$Form = new Tg_Site_Form_Page ($Page);
    	$Form->setAction('/admin/site/page-edit-properties');
    	if ($this->_request->isPost()) {
	        if ($Form->isValid($_POST)) {
				$Page->update ($Form->getValues ());
				$this->view->message = "Updated";
			}
    	} else
    		$Form->setDefaults($Page->toArray());
    		
		$this->view->Page = $Page;
		$this->view->form = $Form;
    }

    public function pagePropertiesDeleteAction ()
    {
		if (!($Page = Tg_Site::getInstance ()->getPageById($this->_getParam ('id'))))
			throw new Exception ("Page not found");
		
		$Page->propertiesXML = '';
    	$Page->save ();
    	
    	echo 'cleared';
    	die;
    	
    }

    public function pageDeleteAction ()
    {
		$Page = Tg_Site::getInstance ()->getPageById($this->_getParam ('id'));
		if (!$Page)
			throw new Exception ("Page not found");
			
		if ($Page->locked)
			throw new Exception ("Page is locked");
		
		$Parent = $Page->getParent();
    	$Page->delete();
    	
		$this->_redirect('/admin/site/pages?id='.$Parent->id);
    }

    public function pageReorderAction ()
    {     	
    	$json = stripslashes($_GET['json']);
     	$phpNative = Zend_Json::decode($json);
     	$root = $phpNative[0];
     	$this->_rightLeft ($root);
     	
     	$this->_save($root); 

     	echo 'Updated';
    	die;
    }
    
    private function _rightLeft (&$page, $left=1) 
    {
    	$page['left']=$left;
	    $left+=1;
    	if (isset ($page['children'])){
    		for ($i=0;$i<count ($page['children']);$i++) {
	    		$left = $this->_rightLeft ($page['children'][$i], $left);
	    		$left+=1;
	    	}
    	}
    	$page['right']=$left;
    	return $left;
    }
    
    private function _save ($page) {
    	$db = Zend_Registry::get ('db');
    	$data = array (
    	'left'=>$page['left'],
    	'right'=>$page['right']
    	) ;
    	$db->update ('site_page', $data, 'id='.$page['id']);
    	if (isset ($page['children'])) {	    	
	    	for ($i=0;$i<count ($page['children']);$i++) {
	    		$this->_save ($page['children'][$i]);
	    	}
    	}
    }
    
    
    public function pageListAction () {
    }
    
    public function pageMoveToAction () 
    { 
    	$Pm = Tg_Site::getInstance();    	
    	$Parent = $Pm->getPageById($this->_getParam("parentId"));
    	$Child = $Pm->getPageById($this->_getParam("childId"));
    	$Parent->moveToEnd ($Child);
    }
    
    public function templatesAction () 
    {
    	$this->view->Templates = Tg_Site::getInstance()->getTemplates();
    }
    
    public function templatesParseAction () 
    {
    	// parse templates
    	
    	$pageTemplateTable = new Tg_Site_Db_PageTemplates();
    	
    	$frontController = Zend_Controller_Front::getInstance();
    	
    	$controllerDirs = $frontController->getControllerDirectory();
    	
    	echo '<pre>';
    	
    	foreach ($controllerDirs as $controllerDir) {
    		
    		if (is_dir($controllerDir)) {
    			$moduleName = basename(realPath($controllerDir.'/../'));
    			
    			$moduleNameH = str_replace('_',' ',$moduleName);
    			$moduleNameH = ucwords($moduleName);
				$moduleNameMin = strtolower($moduleName);
    			
				$dir = new DirectoryIterator($controllerDir);
				
				while ($dir->valid ())
				{
					$fileName = $dir->getFilename();
					if (strpos($fileName,'Controller')>0) {
						try {
							$controllerClassName = substr($fileName,0,strpos($fileName,'.'));
							$controllerClassNameH = substr($fileName,0,strpos($fileName,'Controller'));
							$controllerClassNameMin = strtolower($controllerClassNameH);
							include_once $controllerDir.'/'.$fileName;
							
							if (!class_exists($controllerClassName,false)) {
								$controllerClassName = $this->_formatName($moduleName.'_'.$controllerClassName, false);
							}
							
							$reflector = new ReflectionClass($controllerClassName);
							$visible = $reflector->getStaticPropertyValue('_visible', true);
							
							$pageTemplate = $pageTemplateTable->getByModuleAndController($moduleName, $controllerClassNameMin);
							
							if ($pageTemplate == null) { 
    							echo 'Found: '.$moduleNameH.'-'.$controllerClassNameH .'<br>';
								$data = array (
									'module'=>$moduleNameMin,
									'controller'=>$controllerClassNameMin,
									'visible'=>$visible?'yes':'no',
									'name'=>$moduleNameH.' - '.$controllerClassNameH
								);
								
								$pageTemplate = $pageTemplateTable->createRow($data);
								$pageTemplate->save ();
							}
//							if ($visible) {
							
//							$methods = $reflector->getMethods();
//							
//							foreach ($methods as $method)
//							{
//								if (strpos($method->name, 'Action')>0) {
//									echo '    '.$method->name.'<br>';									
//								}
//							}
//							}
						} 
						catch (Exception $e) 
						{
							echo 'Could no instantiate controller: '.$controllerClassName.' - ignoring<br>';
							echo ' ('.$e->getMessage().')<br>';
						}
					}
						
					$dir->next();
				}
    		}
    	}
    	
    	echo '</pre>';
    }    

    protected function _formatName($unformatted)
    {
        $segments = explode('_', $unformatted);

        foreach ($segments as $key => $segment) {
            $segments[$key] = ucwords($segment);
        }

        return implode('_', $segments);
    }
    
    public function templateListAction () {
    	
    	$this->view->Templates = Tg_Site::getInstance()->getTemplates();
    }
    
    public function templateAddAction () {	
		
    	$PageTemplates = new Tg_Site_Db_PageTemplates ();
    	$Form = new Tg_Site_Form_PageTemplate ();
    	$Form->setAction('/admin/site/template-add');
    	if ($this->_request->isPost()) {
	        if ($Form->isValid($_POST)) {
	        	$data = $Form->getValues();
	        	unset($data['submit']);
				$PageTemplates->insert($data);
				$this->view->form = "Add successful";
				$this->view->updateTemplateList = true;
			}
    	}
		$this->view->title = "New Template";
		$this->view->form = $Form;
		$this->render('template-form');
		
    }
    
    public function templateEditAction () {
		
    	$id = $this->_getParam ('id');
    	$PageTemplate = Tg_Site::getInstance()->getTemplate($id);
    	if (!$PageTemplate)
    		throw new Exception ("PageTemplate not found: ".$id);
    	
    	$Form = new Tg_Site_Form_PageTemplate ();
    	$Form->setAction('/admin/site/template-edit');
    	if ($this->_request->isPost()) {
	        if ($Form->isValid($_POST)) {
	        	$PageTemplate->setFromArray ($Form->getValues());
	        	$PageTemplate->save();
				$this->view->form = "Edit successful";
				$this->view->updateTemplateList = true;
			}
    	} else
    		$Form->setDefaults($PageTemplate->toArray());
		$this->view->title = "Edit Template";
		$this->view->form = $Form; 	
		$this->render('template-form');

    }
    
    public function templateDeleteAction () {
    	$id = $this->_getParam ('id');
    	$PageTemplate = Tg_Site::getInstance()->getTemplate($id);
    	if (!$PageTemplate)
    		throw new Exception ("PageTemplate not found: ".$id);
    	
		$pages = new Tg_Site_Db_Pages();
		$pageRows = $pages->fetchAll("templateId=".$PageTemplate->id);

		if ($pageRows->count()>0)
    		throw new Exception ('PageTemplate '.$PageTemplate->name.' is assigned to one or more pages');
    		
    		
    	$PageTemplate->delete ();
    	
    	echo 'Template deleted';
		$this->view->updateTemplateList = true;
    	
    	die;
    }  
    
    public function layoutsAction () {
    	$this->view->layouts = Tg_Site::getThemes();
    }
    
    public function layoutListAction () {
    	$this->view->layouts = Tg_Site::getThemes();
    }
}

?>