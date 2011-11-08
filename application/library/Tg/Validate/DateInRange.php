<?php
/**
 * Zend Framework
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
 * @category   Zend
 * @package    Zend_Validate
 * @copyright  Copyright (c) 2005-2009 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id: Date.php 17696 2009-08-20 20:12:33Z thomas $
 */

/**
 * @see Zend_Validate_Abstract
 */
require_once 'Zend/Validate/Date.php';

/**
 * @category   Zend
 * @package    Zend_Validate
 * @copyright  Copyright (c) 2005-2009 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Tg_Validate_DateInRange extends Zend_Validate_Date
{
    const NOT_IN_RANGE    = 'dateNotInRange';
    
    /**
     * Validation failure message template definitions
     *
     * @var array
     */
    protected $_messageTemplates = array(
        self::NOT_IN_RANGE => "'%value%' is not of the desired range"
    );
    
    protected $_startDate;
    protected $_endDate;

    public function __construct($startDate, $endDate, $format = null, $locale = null)
    {
    	parent::__construct($format, $locale);
    	
    	$this->_startDate = $startDate;
    	$this->_endDate = $endDate;
    }

    public function isValid($value)
    {
		if (parent::isValid($value))
		{
			// TODO - add test for format and locale == null
			$date = new Zend_Date ($value, $this->_format, $this->_locale);		
			
			if ($date->isEarlier($this->_startDate) || $date->isEarlier($this->_endDate)){
				$this->_error(self::NOT_IN_RANGE);
				return false;				
			}
			
			return true;
			
		} else 
			return false;
    }
}
