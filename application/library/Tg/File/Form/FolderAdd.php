<?php 
class Tg_File_Form_FolderAdd extends Tg_Form
{
	
	public function __construct($options = array ())
	{
		parent::__construct($options);
		
		$this->setAction ('/admin/files/folder-add');
		
		$this->addElement('hidden', 'folder_id', array (
			'decorators' 	 => array('ViewHelper'),
		));

		$this->addElement('text', 'name', array (
			'label' 	 => 'Name',
			'required'	=>true,
			'class' 	 => 'text'
		));

		$this->addElement('text', 'path', array (
			'label' 	 => 'Path',
			'class' 	 => 'text'
		));
		
		$this->addElement('submit', 'submit', array (
			'label' => 'Save',
			'class' => 'save'
			));
	}	
}

?>