<?php
/**
 * Created by JetBrains PhpStorm.
 * User: thomas
 * Date: 22/02/12
 * Time: 11:36 AM
 * To change this template use File | Settings | File Templates.
 */
class Zeph_Config
{
	protected static $_config = null;
    protected static $_configPath = "";

    /**
     * @return Zend_Config_Ini
     */
    public static function getConfig ()
    {
        if(self::$_config === null) {
            self::$_configPath = self::findConfigPath();
            try {
                self::$_config = new Zend_Config_Ini(self::$_configPath, self::getConfigName());
            } catch (Exception $e)
            {
                self::$_config = new Zend_Config_Ini(self::$_configPath, 'instance', array('ignoreExtends'=>true));
            }

        }
        return self::$_config;
    }

    /**
     * @return Zend_Config
     */
    static public function getConfigModifiable ()
    {
        return new Zend_Config_Ini(self::findConfigPath(), null, array('skipExtends'=> true,'allowModifications' => true));
    }

    public static function config ($path)
    {
        $config = self::getConfig()->toArray();

        $configPath = explode('.', $path);

        foreach ($configPath as $path) {
            if (isset($config[$path]))
            {
                $config = $config[$path];
            } else
                return null;
        }
        return $config;
    }

    /**
     * @return string
     * @throws Exception
     */

    static public function findConfigPath ()
    {
        $iniFile = 'application.ini';
        $pathsToTry=array();

        // relative to script filename
        $scriptPath = dirname($_SERVER['SCRIPT_FILENAME']);
        $pathsToTry[] = $scriptPath.'/application/config/'.$iniFile;
        $pathsToTry[] = $scriptPath.'/../application/config/'.$iniFile;

        // relative to current file (Zeph_Core.php)
        $thisPath = dirname(__FILE__);
        $pathsToTry[] = $thisPath.'/../../application/config/'.$iniFile;

        foreach($pathsToTry as $path)
        {
            if (file_exists($path))
                return $path;
        }
        throw new Exception('Unable to find config file');
    }

    /**
     * @return string
     */
    public static function getConfigName ()
    {
        return 'host_'.$_SERVER['SERVER_NAME'];
    }
}