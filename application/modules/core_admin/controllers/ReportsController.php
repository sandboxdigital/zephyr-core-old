<?php

class Core_Admin_ReportsController extends Tg_Site_Controller
{
    public function indexAction() 
    {
    	$form = new Tg_Reports_Form(); 
    	
    	$reports = Tg_Config::get('reports');

    	if (isset($reports) && is_array($reports))
    	{
	    	$options = array ();	
	    	foreach ($reports as $key=>$report)
	    		$options[$key] = $report['name']; 
    		$form->report->setMultiOptions($options);
    	}
    	else
    		$form->report->setMultiOptions(array('No reports'));
    		
		if($this->_request->isPost()) {
			if($form->isValid($this->_request->getPost())) {
				$reportId = $form->report->getValue();
		    	$this->_helper->layout->disableLayout();
			    $this->_helper->viewRenderer->setNeverRender();
				    
				$db = Zend_Registry::get('db');

				$stmt = $db->query($reports[$reportId]['sql']);
				
				$stmt->setFetchMode(Zend_Db::FETCH_ASSOC);
				$rows = $stmt->fetchAll ();
				$rowset = new Tg_Db_Table_Rowset(array('data'=>$rows));

				$rowset->exportToCsv ($reportId. date('Y-m-d') .'.csv');
			}
		} else {
			//$this->view->message = $message;
		}
    	  	
    	
    	$this->view->form = $form;
    }
}

?>