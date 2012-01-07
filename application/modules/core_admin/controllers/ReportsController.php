<?php

class Core_Admin_ReportsController extends Tg_Site_Controller
{
    public function indexAction() 
    {
    	$form = new Tg_Reports_Form(); 
    	
    	$config = Zend_Registry::get('config');
    	$reports = $config['reports'];
    	    	
    	if (is_array($reports))
    	{
	    	$options = array ();	
	    	foreach ($config['reports'] as $key=>$report)
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
				    
//				$t = new Tg_Reports_DbTable();
				$db = Zend_Registry::get('db');
//				$s = $t->select ()
//					->from($t,	array('date','time','name','title','campaignDate','deadline','phone','type'))
//					->where('date>=?', Zend_Date::now()->get('YYYY-MM-dd'))
//					->order('date')
//					->order('time');
								
//				$rows = $db->fetchObjects($reports[$reportId]['sql']);
				
//				$stmt  = new Zend_Db_Statement();
				
				$stmt = $db->query($reports[$reportId]['sql']);
				
				$stmt->setFetchMode(Zend_Db::FETCH_ASSOC);
				$rows = $stmt->fetchAll ();
				$rowset = new Tg_Db_Table_Rowset(array('data'=>$rows));
//				
//				while ($row = $stmt->fetchObject('Tg_Db_Table_Row'))
//					dump ($row);
//				}
//				die;
				$rowset->exportToCsv ($reportId. date('Y-m-d') .'.csv');
				
				//$this->render ('thanks');
			}
		} else {
			//$this->view->message = $message;
		}
    	  	
    	
    	$this->view->form = $form;
    }
}

?>