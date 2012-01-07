<?php
class Tg_Db_Table_Rowset extends Zend_Db_Table_Rowset_Abstract
{    
    function exportToCsv ($fileName = '', $addHeader=true)
    {
    	// Controller needs to call
		//    	
		// $this->_helper->layout->disableLayout();
		// $this->_helper->viewRenderer->setNeverRender();
		
    	if (empty($fileName))
    		$fileName = $this->getTable()->info(Zend_Db_Table_Abstract::NAME) . '_'. date('Y-m-d') .'.csv';
    	
		$mydata = array();
		
		foreach ($this as $field)
		{
			$row = $field->toArray();
			if ($addHeader)
			{
				$mydata[] = array_keys($row);
				$addHeader = false;
			}
			$mydata[] = $row;
		}
	
	    header('Content-type: application/octet-stream');
	    header('Content-Disposition: attachment; filename="'.$fileName.'"');
	    
	    $outstream = fopen("php://output", 'w');
	
	    function __outputCSV(&$vals, $key, $filehandler) {
	        fputcsv($filehandler, $vals, ',', '"');
	    }

	    array_walk($mydata, '__outputCSV', $outstream);
	
	    fclose($outstream);
	    die;    	
    }
}
