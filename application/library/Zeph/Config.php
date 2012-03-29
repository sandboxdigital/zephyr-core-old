<?php
/**
 * Created by JetBrains PhpStorm.
 * User: thomas
 * Date: 22/02/12
 * Time: 11:36 AM
 * To change this template use File | Settings | File Templates.
 */
class Zeph_Config extends Zend_Config_Ini
{
	protected static $_instance = null;
	var $_configPath;
	var $_paths;

	public function __construct($configPath, $section = null)
	{
		$this->_configPath = $configPath;

		parent::__construct($this->_configPath, $section);
	}

	/**
	 * Get singleton instance
	 *
	 * @return  Zeph_Config $instance
	 */
	public static function getInstance($configPath = '', $section = null)
	{
		if(self::$_instance === null) {
            try {
			    self::$_instance = new self($configPath, self::getConfigName());
            } catch (Exception $e)
            {
                self::$_instance = new self($configPath, null, array('ignoreExtends'=>true));
            }

		}
		return self::$_instance;
	}

	/**
	 * @return Zend_Config
	 */
	public static function getConfigModifiable ()
	{
		return new Zend_Config_Ini($this->_configPath,
			null,
			array('skipExtends'=> true,
			'allowModifications' => true));
	}

	/**
	 * @return string
	 */
	public function getConfigPath ()
	{
		return $this->_configPath;
	}
}