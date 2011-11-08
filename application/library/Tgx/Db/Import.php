<?php
class Tgx_Db_Import
{
	private $_spec;
	
	/**
	 * 
	 * Enter description here ...
	 * @var Tg_Db_Table
	 */
	private $_table;
	private $_id = 'id';
	private $_ignoreFirst = true;
	
	public function __construct($table, $spec)
	{
		$this->_spec = $spec;		
		$this->_table = $table;

		if (isset($this->_spec['id']))
			$this->_id = $this->_spec['id'];
	}
		
	public function importFile ($file)
	{
		$skipFirstRow = $this->_ignoreFirst;
		ini_set('auto_detect_line_endings', true);

		$fileHandle = $file->open();
		$rows = array();
		while (($data = fgetcsv($fileHandle, 0, ",")) !== FALSE) {
			
			if ($skipFirstRow) {
				// skip
				$skipFirstRow = false;
			} else
				$rows[] = $data;
		}

		$file->close ();
		
		return $this->importRows($rows);
	}
	
	public function importRows ($rows)
	{
		$output = '';
		$count = 1;
		foreach ($rows as $csvRow)
		{
			$id = $csvRow[$this->_spec['cols'][$this->_id]];
			
			$dbRow = $this->getRow($id);
			if (!$dbRow) {
				$dbRow = $this->_table->createRow();
				//$output.= 'Row doesn\'t exist: '.$id.' at row '.$count.'<br>';
				$output.= 'Added '.$id.' at row '.$count.'<br>';
			} else {
				$output.= 'Updated '.$id.' at row '.$count.'<br>';
			}
			$this->importRow($dbRow, $csvRow);
			$count++;
		}
		
		return $output;
	}
	
	private function getRow ($id)
	{
		$row =  $this->_table->fetchRow($this->_table->select(true)->where($this->_id.'=?',$id));
		return $row;
	}
	
	public function importRow ($dbRow, $csvRow)
	{
		foreach ($this->_spec['cols'] as $name => $i)
		{
			if (is_array($i)) {
				if ($i['type'] == 'lookup')
				{
					$val = $csvRow[$i['pos']];
					if (isset($i['values'][$val])) {
						$dbRow->$name =  self::cleanData($i['values'][$val]);
					} else {
						echo 'Unknown look up - '.$val.'<br>';	
					}
				} elseif ($i['type'] == 'multi')
				{
					//
				}
			} else if (is_string($i)) {
				$value = @vsprintf($i, $csvRow);
				$value = self::cleanData($value);
				$dbRow->$name = $value;
			} else {
				// check to see if the csv value is set
				if ($csvRow[(int)$i] != '')
					$dbRow->$name = self::cleanData($csvRow[(int)$i]);
			}
		}
	
		$dbRow->save ();
	}
	
	public static function cleanData ($str)
	{
		$str = str_replace("Õ", "'", $str);
		$str = str_replace("\v", "\r", $str);
//		$str = iconv("Windows-1252", "UTF-8", $str);		
		return $str;
	}
}