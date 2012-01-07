<?php
/**
 * Unique Validator
 *
 * @copyright  Copyright (c) 2008 Delete Limited (http://www.deletelondon.com)
 */

class Tg_Validate_Unique extends Zend_Validate_Abstract
{
    const NOT_UNIQUE = 'valueNotUnique';

    protected $_messageTemplates = array(
        self::NOT_UNIQUE => "'%value%' has already been used",
    );

    protected $_model;
    protected $_field;
	protected $_id;

    public function __construct($model, $field, $id = 0)
    {
		$this->_model = new $model();
		$this->_field = $field;
		
		if($id > 0) {
			$this->_id = $id;
		}
		else {
			$this->_id = 0;
		}
    }

    public function isValid($value)
    {

        $valueString = (string) $value;
        $this->_setValue($valueString);

		$select = $this->_model->select()->where($this->_field.' = ?', $valueString)
										 ->where('id != ?', $this->_id);
		
		if($rows = $this->_model->fetchRow($select)) {
	        $this->_error(self::NOT_UNIQUE);
		}

        if (count($this->_messages)) {
            return false;
        } else {
            return true;
        }
    }

}
