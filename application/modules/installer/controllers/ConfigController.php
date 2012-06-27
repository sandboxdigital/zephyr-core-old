<?php

require 'AbstractController.php';

class ConfigController extends AbstractController
{
	public function init()
	{
		parent::init();

        $this->_config = Zeph_Config::getConfig();
		$this->_configModifiable = Zeph_Config::getConfigModifiable();
	}

    public function indexAction()
    {
	    $sections = array();

	    foreach ($this->_configModifiable as $key=>$section)
		    $sections[] = $key;

	    $this->view->sections = $sections;
	    $this->view->activeSection = Zeph_Config::getConfigName();
	    $this->view->activeSectionExists = $this->getConfig()->getSectionName()==Zeph_Config::getConfigName();
    }

    public function editAction()
    {
	    $form = $this->_getForm();
	    $this->view->messages = array();
	    $this->view->form = $form;
	    $prodHostName = $this->_getParam('section');

	    if ($this->_request->isPost()) {
		    if ($form->isValid($this->_getAllParams()))
	        {
			    $values = $form->getValues();

				if (!isset($this->_configModifiable->$prodHostName))
				{
					$this->_configModifiable->$prodHostName = array ();
					$this->_configModifiable->$prodHostName->resources = array ();
					$this->_configModifiable->$prodHostName->resources->db = array ();
					$this->_configModifiable->$prodHostName->resources->db->params = array ();
				}

			    $this->_configModifiable->setExtend($prodHostName, 'production');

				$this->_configModifiable->$prodHostName->resources->db->params->dbname = $values['dbname'];
				$this->_configModifiable->$prodHostName->resources->db->params->username = $values['username'];
				$this->_configModifiable->$prodHostName->resources->db->params->password = $values['password'];

				// Write the config file
				$writer = new Zend_Config_Writer_Ini(array('config'   => $this->_configModifiable,
					'filename' => Zeph_Config::findConfigPath()));

				$writer->write();
		        $this->view->messages[] = 'Config written';
	        }
	    } else
	    {
		    $form->section->setValue($prodHostName);

			if (isset($this->_configModifiable->$prodHostName))
			{
			    $form->dbname->setValue($this->_configModifiable->$prodHostName->resources->db->params->dbname);
				$form->username->setValue($this->_configModifiable->$prodHostName->resources->db->params->username);
				$form->password->setValue($this->_configModifiable->$prodHostName->resources->db->params->password);
			}
	    }
    }

	private function _getForm ()
	{
		$form = new Zend_Form();
		$form->addElement('text','section',array(
			'label'=>'Host/section name',
			'required'=>true

		));
		$form->addElement('text','dbname',array(
			'label'=>'Db name',
			'required'=>true

		));
		$form->addElement('text','username',array(
			'label'=>'Db user',
			'required'=>true

		));
		$form->addElement('text','password',array(
			'label'=>'Db pass',
			'required'=>true

		));
		$form->addElement('submit','go',array(
			'label'=>'Save',
			'required'=>true
		));

		return $form;
	}
}

