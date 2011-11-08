<?php
class Tgx_Affiliate_Factory
{
	public static $COOKIE_NAME = 'SPONSOR121_affCode';
	
	protected static $_instance = null;

	private function __construct ($options = array())
	{
		//		$this->_options = $options + $this->_defaults;
		//
		//		$this->_messageTable = new Tgx_Messaging_Db_Table_Message();
		//		$this->_mailboxTable = new Tgx_Messaging_Db_Table_Mailbox();
	}

	/**
	 * Get singleton instance
	 *
	 * @return Tgx_Affiliate_Factory
	 */
	public static function instance($options = array())
	{
		if(self::$_instance === null) {
			self::$_instance = new self($options);
		}
		return self::$_instance;
	}

	public static function getAffiliateByCode ($code)
	{
		$table = new Tgx_Affiliate_Db_Table_Affiliate();
		$select = $table->select ()
		->where('code=?',$code);
			
		return $table->fetchRow ($select);
	}
	
	public static function register ($data)
	{
		$table = new Tgx_Affiliate_Db_Table_Affiliate ();
		
		unset($data['password_confirm']);
		unset($data['register']);
		$code = uniqid();
		
		while ($table->exists(array ('code'=>$code)))
			$code = uniqid();
		
		$data['code']=$code;
			
		$row = $table->createRow ($data);
		$row->save ();
		return $row;
	}
	
	public static function setCookie ($affiliate)
	{
		setcookie ( self::$COOKIE_NAME , $affiliate->code, time()+60*60*24*365, '/', $_SERVER['SERVER_NAME']);
	}
	
	public static function getCookie ()
	{
		return Zend_Controller_Front::getInstance()->getRequest()->getCookie (self::$COOKIE_NAME);
	}
	
	public static function trackPurchase ($userId, $amount, $description = null, $affiliateCode = null)
	{
		if ($affiliateCode == null) {
			$affiliateCode = self::getCookie();
		}
		
		if ($affiliateCode)
			$affiliate = self::getAffiliateByCode($affiliateCode);
		else
			$affiliate = null;
			
		if ($affiliate)
		{
			$tPurchase = new Tgx_Affiliate_Db_Table_AffiliatePurchase ();
			
			$data = array (
				'affiliateId'=>$affiliate->id,
				'userId'=>$userId,
				'amount'=>$amount,
				'description'=>$description,
				
			);
			
			$tPurchase->insert ($data);
		}
	}
}