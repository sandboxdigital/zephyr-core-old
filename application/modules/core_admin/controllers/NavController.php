<?php

class Core_Admin_NavController extends Tg_Content_Controller
{
	protected static $_visible = false;

    public function init() {
    	$this->_showPageBar = false;
    	
    	parent::init ();

        $ajaxContext = $this->_helper->getHelper('ajaxContext');
        $ajaxContext->addActionContext('ajax-page-save', 'json')
                    ->initContext('json');
	}
    
	public function indexAction() 
    {
        $this->view->headScript()->appendFile(Tg_Site::getCorePath('js/admin/nav_index.js'));

        $navs = Tg_Nav::getNavs();

        $this->view->pageNodes = Tg_Site::getInstance()->getRootPage()->toJson('write') ;
        $this->view->navNodes = Zend_Json::encode ($navs);
        $this->view->adminPath = Tg_Site::getInstance()->getPage('/admin')->getUrl();
    }

    public function saveAction ()
    {
        $response = new stdClass();
        $response->success = true;

        try {
            $pageId = $this->_getParam ('id',0);
            $parentId = $this->_getParam ('parentId',0);

            if ($pageId<=0 && $parentId<=0)
                throw new Zend_Exception ("Page updated failed - no page id or parentId");
            elseif ($pageId>0) {
                $Page = Tg_Nav::getInstance ()->getNavitemById($pageId);
                if (!$Page)
                    throw new Zend_Exception ("Page updated failed - page not found");

                $Page->update ($_POST);

                $response->msg = "Page updated";
            } elseif ($parentId>0) {
                $Parent = Tg_Nav::getInstance ()->getNavitemById($this->_getParam ('parentId'));
                if (!$Parent)
                    throw new Zend_Exception ("Page updated failed - parent page not found");

                $Page = Tg_Nav::getInstance ()->appendNavitem ($_POST, $Parent);

                $response->msg = "Page added";
            }
            $response->page=$Page->toObject();
        } catch (Zend_Exception $exp)
        {
            $response->success = false;
            $response->msg = $exp->getMessage ();
        }

        echo Zend_Json::encode ($response);
        die;
    }

    public function moveAction ()
    {

        $response = new stdClass();
        $response->success = true;
        $response->msg = 'Success';

        try {
            $Pm = Tg_Nav::getInstance();
            $Parent = $Pm->getNavitemById($this->_getParam("parentId"));
            $Parent->moveNavitem ($this->_getParam("navId"), $this->_getParam("previousSiblingId",0));

        } catch (Zend_Exception $exp)
        {
            $response->success = false;
            $response->msg = $exp->getMessage ();
        }

        echo Zend_Json::encode ($response);
        die;
    }

    public function deleteAction ()
    {
        $Page = Tg_Nav::getInstance ()->getNavitemById($this->_getParam ('id'));
        if (!$Page)
            throw new Exception ("Page not found");

        if ($Page->locked)
            throw new Exception ("Page is locked");

        $Page->delete();

        echo '{"success":true,"msg":"Delete successful"}';
        die;
    }

    public function addAllAction ()
    {

        $page = Tg_Site::getInstance()->getRootPage();
        $menus = Tg_Nav::getNavs();
        $menu = $menus[0]->getRootNavitem();

        $this->addPageToMenu($page,$menu);
    }

    protected function addPageToMenu (Tg_Site_Db_Page $page , Tg_Nav_Db_Navitem $menu)
    {
        $newItem = $menu->appendNavitem(array(
            'title'=>$page->title,
            'page'=>$page->id,
            'type'=>1
        ));


        echo '{"success":true,"msg":"Add all successful"}';
        die;
    }
}