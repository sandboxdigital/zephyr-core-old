<?php

class Tg_Crud_Controller extends Tg_Site_Controller
{
	const FORM_STATE_NEW 		= 'new';
	const FORM_STATE_SUCCESS 	= 'success';
	const FORM_STATE_ERROR 		= 'error';
	
	protected $_model;
	protected $_table;
	protected $_form;
	protected $_url;
	protected $_readOnSuccess 		= true;
	protected $_readOnDelete 		= true;
	protected $_viewCreate			= 'form';
	protected $_viewCreateSuccess	= 'form';
	protected $_viewUpdate			= 'form';
	protected $_viewUpdateSuccess	= 'form';
	protected $_viewRead			= 'list';
	protected $_viewDelete			= '';
	protected $_viewDeleteSuccess	= '';
	
	public function init() 
	{
		parent::init ();
		
		if (empty($this->_table))
			throw new Zend_Exception ("Tg_Crud_Controller->_table not set");
			
		if (empty($this->_url))
		{
			$this->_url = Tg_Site_Utils::TrimTrailSlash($this->_page->url);
		}
			
		if (empty($this->_form))
			throw new Zend_Exception ("Tg_Crud_Controller->_form not set");
		
		$this->view->url = $this->_url;
    }
	
	protected function _getNew ()
	{
		if ($this->_table instanceof Tg_Crud_Factory)
			return $this->_table->getNew ();
		elseif ($this->_table instanceof Tg_Db_Table)
			return $this->_table->fetchNew ();
	}
		
	protected function _getAll ()
	{
		if ($this->_table instanceof Tg_Crud_Factory)
			return $this->_table->getAll ();
		elseif ($this->_table instanceof Tg_Db_Table)
			return $this->_table->fetchAll($this->_table->select());
	}
	
	protected function _getItem ($id)
	{
		if ($id == null)
			throw new Exception ('Tg_Site_Controller error - id is required');
		
		if ($this->_table instanceof Tg_Crud_Factory)
			return $this->_table->getItem ($id);
		elseif ($this->_table instanceof Tg_Db_Table)
			return $this->_table->fetchRow ("id=".$id);
	}
	
	protected function _deleteItem ($id)
	{
		if ($this->_table instanceof Tg_Crud_Factory)
			return $this->_table->deleteItem ($id);
		elseif ($this->_table instanceof Tg_Db_Table)
			return $this->_table->delete ("id=".$id);
	}
	
	protected function _postCreate ($form, $model) 
	{
		
	}

	protected function _postUpdate ($form, $model) 
	{
		
	}

	protected function _postDelete () 
	{
		
	}
	
    public function indexAction() 
    {
		$this->readAction ();
    }

	public function readAction() 
	{
		$this->view->items = $this->_getAll();
		
		$this->render ($this->_viewRead);
	}

	public function createAction($model = null) 
	{
		$this->_form->setAction($this->_url.'/create');
		
		if (empty($model))
			$model = $this->_getNew();
		
		$state = $this->_processForm($this->_form, $model);	
		
		
		//$model = $this->_getItem ($this->_getParam('id'));
	
		if ($state == Tg_Crud_Controller::FORM_STATE_SUCCESS) {
			$this->_postCreate($this->_form, $model);
			if ($this->_readOnSuccess)
				$this->readAction();
			else
				$this->render ($this->_viewCreateSuccess);
		} else
			$this->render ($this->_viewCreate);
	}

	public function updateAction() 
	{
		$this->_form->setAction($this->_url.'/update');
		
		$model = $this->_getItem ($this->_getParam('id'));
		
		$state = $this->_processForm($this->_form, $model);	
	
		if ($state == Tg_Crud_Controller::FORM_STATE_SUCCESS) {
			$this->_postUpdate($this->_form, $model);
			if ($this->_readOnSuccess)
				$this->readAction();
			else
				$this->render ($this->_viewUpdateSuccess);
		} else
			$this->render ($this->_viewUpdate);
	}

	function deleteAction() {
		$id = (int)$this->_getParam('id');
		if ($id > 0) {			
			$this->_deleteItem ($id);
			$this->_postDelete();
			if ($this->_readOnDelete)
				$this->readAction ();
			else
				$this->render ($this->_viewDeleteSuccess);
		}
	}

	function viewAction() {		
		$model = $this->_getItem ($this->_getParam('id'));
		
		if (!isset($model))
			throw new Exception ('Not found');	
					
		$this->view->modelLabels = $this->_modelLabels;
		$this->view->model = $model;
	}
	
	
	
	protected function _processForm ($form, $model)
	{		
		$this->_model = $this->view->model = $model;
		$this->view->form = $form;
		
		if ($model) {
			if (empty($model->id))
				$form->removeElement('id');
		}
		
		if ($this->_request->isPost()) {
			$formData = $this->_request->getPost();
			if($form->isValid($formData)) {
				$formData = $form->getValues();
				$model->setFromArray($formData);
				$model->save();
				return self::FORM_STATE_SUCCESS;
			} else {
				return self::FORM_STATE_ERROR;
			}
		} else {
			if ($model) {
//				if (empty($model->id))
//					$form->removeElement('id');
				
				$form->populate($model->toArray());
			}
			return self::FORM_STATE_NEW;
		}
	}
}