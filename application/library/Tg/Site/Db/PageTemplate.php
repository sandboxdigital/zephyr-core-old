<?php
/**
 * Tg Site Page Database Gateway Class 
 *
 * @copyright  Copyright (c) 2009 Thomas Garrood (http://www.garrood.com)
 *
 */

class Tg_Site_Db_PageTemplate extends Tg_Db_Table_Row  
{
	function toObject ()
    {
    	return array (
	    	'name'=>$this->name,
	    	'id'=>$this->id,
	    	'visible'=>$this->visible,
	    	'defaultSubPageTemplate'=>$this->defaultSubPageTemplate
	    	);
	}
	
	
	/**
	 * Enter description here ...
	 * 
	 * @return Tg_Content_Data $data
	 */
	public function getContent ($version = 0)
	{
		$contentId = 'SitePageTemplate'.$this->id;
		$content = Tg_Content::getContent($contentId, $version);
	    return $content->content();
	}
	
	function toJson ()
	{
		return Zend_Json_Encoder::encode($this->toObject ());
	}
}
?>