<?php

//
//class Model_Tour extends Tg_Db_ActiveRecord
//{
//    protected $_name = 'tour';
//    protected $_className = __CLASS__;
//	private static $_model;
//
//    /**
//     *
//     * @param type $name
//     * @return Model_ARTour
//     */
//    public static function model($name = __CLASS__)
//    {
//        if (!self::$_model)
//            self::$_model = new $name();
//
//        return self::$_model;
//    }
//}
//

class Tg_Db_ActiveRecord extends Tg_Db_Table_Row_Crud
{
	protected $_className;
	protected $_name;

	function __construct($config = array())
	{
		if (isset($config['data']) && isset($config['table']))
		{
			parent::__construct($config);
		} else
		{
			$table = Tg_Db_ActiveRecord_Factory::getActiveRecordTable($this->_name, $this->_className);
			
	        $cols     = $table->info(Zend_Db_Table::COLS);
	        $defaults = array_combine($cols, array_fill(0, count($cols), null));
			
			parent::__construct(array(
				'table'		=>$table,
	            'data'     => $defaults,
	            'readOnly' => false,
	            'stored'   => false
				//'data'=>$config
				));
			$this->setFromArray($config);
		}
	}

	public function fetchPairs ($value, $key='id', $where=null, $order=null)
	{
		return $this->_table->fetchPairs ($key, $value, $where, $order);
	}

	public function fetchAll ($where = null, $order = null, $count = null, $offset = null)
	{
//		$args = func_get_args();
		//dump($this->_table);
		return $this->_table->fetchAll($where, $order, $count, $offset);
	}

	public function all ($where = null, $order = null, $count = null, $offset = null)
	{
//		$args = func_get_args();
		return $this->_table->fetchAll($where, $order, $count, $offset);
	}

	public function allIndexed ($where = null, $order = null, $count = null, $offset = null)
	{
		$items = $this->_table->fetchAll($where, $order, $count, $offset);
		$itemsIndexed = array();
		foreach ($items as $item)
		{
			$itemsIndexed[$item->id] = $item;
		}
		
		return $itemsIndexed;
	}

	public function fetchRow ($where = null, $order = null)
	{
//		$args = func_get_args();
		return $this->_table->fetchRow($where, $order);
	}

	public function find ($id)
	{
		return $this->_table->fetchRow('id='.$id);
	}

	public function select ($withFromPart=false)
	{
		return $this->_table->select($withFromPart);
	}
}