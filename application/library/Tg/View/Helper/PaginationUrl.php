<?php
class Tg_View_Helper_PaginationUrl extends Zend_View_Helper_Abstract
{
	function paginationUrl ($queryParams = array ())
	{
        $query = Zend_Controller_Front::getInstance()->getRequest()->getQuery();  
        
		$url = $this->view->url(); 
       
        if(!empty($queryParams))  
        {  
            $queryParams = (array)$queryParams;  
//            $query = explode("&", $query);  
  
            $add = '/?';

            foreach($queryParams as $key=>$value)  
            {  
            	$query[$key] = $value;
            } 
        }            

        return $url . '?'. http_build_query($query); ;  
	}
}