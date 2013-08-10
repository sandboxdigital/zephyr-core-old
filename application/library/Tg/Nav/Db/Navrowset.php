<?php
/**
 * Tg Site Page Database Gateway Class
 *
 * @copyright  Copyright (c) 2009 Thomas Garrood (http://www.garrood.com)
 *
 */

class Tg_Nav_Db_Navrowset extends Tg_Db_Table_Rowset
{
    public function toJson ()
    {
        $navs = array();

        foreach ($this as $nav)
        {
            $navs[] = $nav->toObject();
        }
        return Zend_Json::encode ($navs);
    }
}