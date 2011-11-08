<?php 
class Tg_File_Form_UploadFile extends Tg_Form
{
	
	public function __construct($options = array ())
	{
		parent::__construct($options);
		
		$this->setAction ('/admin/files/file-upload');
		
		$this->addElement('hidden', 'folder_id', array (
			'decorators' 	 => array('ViewHelper'),
		));

		$this->addElement('fileUpload', 'file_id', array (
			'label' 	 => 'File',
			'class' 	 => 'file'
		));
		
		$this->addElement('submit', 'submit', array (
			'label' => 'Save',
			'class' => 'save'
			));
		
	}	
}

?>