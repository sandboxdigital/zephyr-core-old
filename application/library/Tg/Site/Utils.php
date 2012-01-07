<?php
/**
 * Tg Framework 
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://framework.zend.com/license/new-bsd
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@zend.com so we can send you a copy immediately.
 *
 * @copyright  Copyright (c) 2009 Thomas Garrood (http://www.garrood.com)
 * @license    New BSD License
 */
/**
 * Tg Site Utilities Class 
 *
 * @copyright  Copyright (c) 2009 Thomas Garrood (http://www.garrood.com)
 *
 */
class Tg_Site_Utils {
	
	static function populateForm (&$Element, &$Form) {	
		$keys = array_keys($Element->Fields);
		for ($i=0;$i<count($keys);$i++) {
			$Field = &$Element->Fields[$keys[$i]];
			$FormField =& $Form->addElementX(array ('type'=>$Field->type, 'name'=>$Field->name));
			$Field->copyToForm ($FormField);
		}
	}
	
	static function populateElement (&$Element, &$Form) {		
		$keys = array_keys($Element->Fields);
		for ($i=0;$i<count($keys);$i++) {
			$Field = &$Element->Fields[$keys[$i]];
			$FormField =& $Form->addElementX(array ('type'=>$Field->type, 'name'=>$Field->name));			
			$Field->copyFromForm ($FormField);
		}
	}
	
	static function copy (&$src, &$dest) {	
		$keys = array_keys($src->Fields);
		for ($i=0;$i<count($keys);$i++) {
			$Field = &$src->Fields[$keys[$i]];
			if ($dest->Fields[$Field->name]) {				
				if ($Field->type=="file") {
					$Field->Element=null;
					$dest->Fields[$Field->name]->File = $Field->File;
					$dest->Fields[$Field->name]->fileId = $Field->fileId;
				} else
					$dest->Fields[$Field->name]->value = $Field->value;
			}
		}
	}
	
	static function printPages () {
		$Pm = Tg_Site::getInstance();
		$return = '<ul class="PmPageList">';
		$return .= Tg_Site_Utils::printPage ($Pm->getPage ('/'));
		$return .= '</ul>';
		return $return;
	}

	static function printPage (&$PmPage, $controls=false) {
		$return = '<li class="PmPageListPage'.($PmPage->left==1?' PmRoot':'').'" pageid="'.$PmPage->id.'">';
		$return .= '<div><img src="/core/images/icons/icon_file.gif" class="controlLeft move" title="Move" pageid="'.$PmPage->id.'" /><a href="/admin/site/page-edit?id='.$PmPage->id.'" class="name inPageForm">'.$PmPage->title.'</a>';
		$return .= '<a href="/admin/site/page-delete?id='.$PmPage->id.'" class="control delete" title="Delete">Delete</a>';
		$roles = $PmPage->getRoles ();
		if (count($roles)>0)
			$return .= '<a href="/admin/site/page-roles?id='.$PmPage->id.'" class="control roles inPageForm" title="Roles">Roles</a>';
		else
			$return .= '<a href="/admin/site/page-roles?id='.$PmPage->id.'" class="control roles_faded inPageForm" title="Roles">Roles</a>';
		$return .= '</div>';
		//$return .= '<a href="/admin/site/page-add?page='.$PmPage->url.'" class="control add" title="Add">Add</a>';
		if (count($PmPage->Pages)>0) {
			$return .= '<ul class="'.($PmPage->left==1?'PmRootUl':'').'">';
			foreach ($PmPage->Pages as $subPage) {
				$return .= Tg_Site_Utils::printPage ($subPage);
			}
			$return .= '</ul>';
		}
		$return .= '</li>';
		return $return;
	}

	static function printPagesAsUL ($PmPage) {
		$return = '';
		if (count($PmPage->Pages)>0) {
			$return .= '<ul>';
			foreach ($PmPage->Pages as $subPage) {
				$return .= '<li><a href="'.$subPage->url.'">'.$subPage->title.'</a></li>';
			}
			$return .= '</ul>';
		}
		return $return;
	}

	static function printPageTemplates () {
		global $Pm;
		
		echo '<ul class="PmTemplateList">';
		foreach ($Pm->Site->PageTemplates as $PageTemplate) {
			echo '<li style="padding-left:18px;">
			<a href="?templateId='.$PageTemplate->id.'&action=EDIT" class="name">'.$PageTemplate->name.'</a><a href="?templateId='.$PageTemplate->id.'&action=DELETE" class="control delete" title="Delete">Delete</a>			
			</li>';
		}	
		echo '</ul>';
		echo '<div><a href="?action=ADD" class="controlLeft add" title="Add">Add</a></div>';
	}
	
	static function printElementTemplates () {
		global $Pm;
		
		echo '<ul class="PmTemplateList">';
		foreach ($Pm->Site->ElementTemplates as $ElementTemplate) {
			echo '<li style="padding-left:18px;">
			<a href="?templateId='.$ElementTemplate->id.'&action=EDIT" class="name">'.$ElementTemplate->name.'</a><a href="?templateId='.$PageTemplate->id.'&action=DELETE" class="control delete" title="Delete">Delete</a>			
			</li>';
		}	
		echo '</ul>';
		echo '<div><a href="?action=ADD" class="controlLeft add" title="Add">Add</a></div>';
	}
		
		
	// -=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
	// generic path functions
	// -=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-

	
	static function GetLastSegment ($path) 
	{
		$path = self::TrimQueryString($path);
		$path = self::TrimLeadingSlash($path);
		$path = self::TrimTrailSlash($path);
		
		$pathArray = explode('/',$path);
    	return $pathArray[count($pathArray)-1];
	}
	
	// -=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
	// TrimLeadingSlash
	
	static function TrimLeadingSlash ($path) {
		if (strpos($path,"/")===0){
			$path = substr ($path,1); // strip slash
		}
		return $path;
	}
	
	// -=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
	// TrimTrailSlash
	
	static function TrimTrailSlash ($path) {
		if (strrpos ($path,"/")==(strlen($path)-1))
			$path = substr ($path,0,strlen($path)-1); // strip slash
		return $path;
	}
	
	static function TrimQueryString ($path) {
		if (strrpos ($path,"?")>0)
			$path = substr ($path,0,strrpos ($path,"?")); // strip slash
		return $path;
	}
	
	// -=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
	// GetParentPath
	
	static function GetParentPath ($path) {
		$path = TrimTrailSlash ($path);
		$path = substr ($path,0,strrpos ($path,"/"));
		return $path;
	}
	
	// -=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
	// GetPageID
	
	static function GetPageID ($path) {
		$aPaths = explode ("/", $path);
		return $aPaths[count($aPaths)-1];
	}
	
	// -=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
	// AbsDirPath
	
	static function AbsDirPath ($dir) { 
		return PATH_CONTENTROOT.$dir;
	}
}
?>