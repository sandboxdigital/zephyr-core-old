<?php
/**
 * Tg Site Page Database Gateway Class
 *
 * @copyright  Copyright (c) 2009 Thomas Garrood (http://www.garrood.com)
 *
 */

class Tg_Nav_Db_Nav extends Tg_Db_Table_Row
{
	private $_items;
    private $_rootItem;

    public function loadItems ()
    {
        if (!$this->_items)
        {
            $this->_items = array();

            $pages = new Tg_Nav_Db_Navitems();
            $rows = $pages->fetchAll('nav_id='.$this->id,'left');
            if (count($rows)==0) {
                $data = array (
                    'title'=>'ROOT',
                    'nav_id'=>$this->id,
                    'left'=>'1',
                    'right'=>'2',
                    'type'=>'0'
                );
                $pages->insert($data);
                $rows = $pages->fetchAll('nav_id='.$this->id,'left');
            }

            $this->_rootItem = $rows->current();
            $this->_rootItem->initNavitem (null, $this);
            $rows->next();
            $this->_rootItem->populatNavitems ($rows, $this);
        }
    }

	public function toJSON ()
	{
		return Zend_Json::encode ($this->toObject());
	}

    public function toObject ()
    {
        $this->loadItems();

        return array(
            'name'=>$this->name,
            'id'=>$this->id,
            'items'=>$this->_rootItem->toObject()
        );
    }
}
?>