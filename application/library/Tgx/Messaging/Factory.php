<?php
/**
 * Tg Framework
 *
 * @copyright  Copyright (c) 2009 Thomas Garrood (http://www.garrood.com)
 */

class Tgx_Messaging_Factory 
{
	private $_defaults = array () ;
		
	protected static $_instance = null;
	protected $_currentUser = null;
	protected $_options = null;
	protected $_messageTable = null;
	protected $_mailboxTable = null;
	protected $_inbox = null;
	protected $_outbox = null;

	const MAILBOX_TYPE_INBOX		= 0;
	const MAILBOX_TYPE_OUTBOX		= 1;

	const MESSAGE_STATUS_UNREAD		= 'unread';
	const MESSAGE_STATUS_READ		= 'read';
	const MESSAGE_STATUS_DELETED	= 'unread';
	const MESSAGE_STATUS_PENDING	= 'pending';
	
	private function __construct ($options=array()) 
	{
		$this->_options = $options + $this->_defaults;
		
		$this->_messageTable = new Tgx_Messaging_Db_Table_Message();
		$this->_mailboxTable = new Tgx_Messaging_Db_Table_Mailbox();
	}
	
    /**
     * Get singleton instance
     *
     * @return Tgx_Messaging
     */
	public static function instance($options=array()) 
	{
		if(self::$_instance === null) {
			self::$_instance = new self($options);
		}
		return self::$_instance;
	}
	
	/**
	 * Sends a message using the internal mail system
	 *  
	 * @param $to
	 * @param $subject
	 * @param $body
	 * @param $from = null 
	 * @return Tgx_Messaging_Db_Message
	 */
	public static function sendMessageTemplate ($to, $subject, $body, $from = null, $anonymous = false)
	{
		
	}
	
	/**
	 * Sends a message using the internal mail system
	 *  
	 * @param $to
	 * @param $subject
	 * @param $body
	 * @param $from = null 
	 * @return Tgx_Messaging_Db_Message
	 */
	public static function sendMessage ($to, $subject, $body, $from = null, $fromName = null)
	{
		if ($from === 0)
		{
			// from anonymous			
			$toUser = Tg_User::getUserById($to);
			
			if (!isset($toUser))
				throw new Zend_Exception('No to user');
			
			$inst = self::instance();
			$toInbox = self::getUsersInbox ($to);
			
			$toMessage = $inst->_messageTable->createRow (array (
				'mailbox_id'=>		$toInbox->id,
				'to_user_id'=>		$toUser->id,
				'from_user_id'=>	0,
				'fromName' =>		$fromName,
				'subject'=>			$subject,
				'body'=>			$body,
				'status'=>			Tgx_Messaging_Factory::MESSAGE_STATUS_UNREAD
			));	
			$toMessage->save ();	
		} else {
			if (empty($from)) {
				$fromUser = Tg_Auth::getAuthenticatedUser();
			} else
				$fromUser = Tg_User::getUserById($from);
				
			if ($fromName == null)
				$fromName = $fromUser->name;
			
			$toUser = Tg_User::getUserById($to);
			
			if (!isset($toUser))
				throw new Zend_Exception('No to user');
			if (!isset($fromUser))
				throw new Zend_Exception('No from user');
			
			$inst = self::instance();
			$toInbox = self::getUsersInbox ($to);
			$fromOutbox = self::getUsersOutbox($from);
			
			$status = Tgx_Messaging::MESSAGE_STATUS_UNREAD;
			
			if ($toUser->isRightsholder == 'yes' && $toUser->isPremiumRightsholder == 'no' && !Model_User::hasCredit($toUser->id, $fromUser->id))
			{
				$status = Tgx_Messaging::MESSAGE_STATUS_PENDING;
				
				$data = array 
				(
				'name'=>$toUser->name
				);
				
				// send email to rightsholder
				$email = new Tg_Mail ();
				$email->to = $toUser->email;
				$email->to = 'thomas.garrood@gmail.com';
				$email->subject = 'New message';
				$email->template = 'notification-rightsholder-upgrade';
				$email->variables = $data;
				$email->send();
			}
			
			$toMessage = $inst->_messageTable->createRow (array (
				'mailbox_id'=>$toInbox->id,
				'to_user_id'=>$toUser->id,
				'from_user_id'=>$fromUser->id,
				'fromName' =>	$fromName,
				'subject'=>$subject,
				'body'=>$body,
				'status'=>$status
			));	
			$toMessage->save ();	
			
			$fromMessage = $inst->_messageTable->createRow (array (
				'mailbox_id'=>$fromOutbox->id,
				'to_user_id'=>$toUser->id,
				'from_user_id'=>$fromUser->id,
				'fromName' =>	$fromName,
				'subject'=>$subject,
				'body'=>$body,
				'status'=>Tgx_Messaging_Factory::MESSAGE_STATUS_UNREAD
			));		
			$fromMessage->save ();
			
		
		}
		if ($toUser->isRightsholder == 'yes' && $toUser->rightsholderNotification == 'yes')
		{
			$data = array 
				(
				'name'=>$toUser->name
				);
			// send email to rightsholder
			$email = new Tg_Mail ();
			$email->to = $toUser->email;
//			$email->to = 'thomas.garrood@gmail.com';
			$email->subject = 'New message';
			$email->template = 'notification-rightsholder';
			$email->variables = $data;
			$email->send();
		}
		return $toMessage;
	}
	
	public static function getMessage ($id, $userId = null)
	{
		$inst = self::instance();
		return $inst->_messageTable->getMessage($id, $userId);
	}
	
	public static function setMessageStatus ($id, $status)
	{
		$inst = self::instance();
		$inst->_messageTable->update(array('status'=>$status),'id='.$id);
	}
	
	public static function deleteMessage ($id)
	{
		$inst = self::instance();
		$inst->_messageTable->update(array('status'=>'deleted'),'id='.$id);
	}
	
	/**
	 *  Gets the inbox of the specified user
	 *  
	 * @param $userId - if not id specified then gets the inbox of the current user
	 * @return Tgx_Messaging_Db_Mailbox
	 */
	public static function getUsersInbox ($userId = null)
	{	
		if (empty($userId)) {
			$user = Tg_Auth::getAuthenticatedUser();
			$userId = $user->id;
		}
			
		return self::getUsersMailbox ($userId, Tgx_Messaging_Factory::MAILBOX_TYPE_INBOX, 'Inbox');
	}
	
	/**
	 *  Gets the outbox of the specified user
	 *  
	 * @param $userId
	 * @return Tgx_Messaging_Db_Mailbox
	 */
	public static function getUsersOutbox ($userId = null)
	{	
		if (empty($userId)) {
			$user = Tg_Auth::getAuthenticatedUser();
			$userId = $user->id;
		}
		
		return self::getUsersMailbox ($userId, Tgx_Messaging_Factory::MAILBOX_TYPE_OUTBOX, 'Outbox');
	}
	
	/**
	 *  Gets the mailbox of the specified user
	 *  
	 * @param $userId
	 * @return Tgx_Messaging_Db_Mailbox
	 */
	public static function getUsersMailbox ($userId, $type, $name)
	{	
		$inst = self::instance();
		
		$mailbox = $inst->_mailboxTable->fetchRow (array(
			'user_id=?'=>$userId,
			'type=?'=>$type));
		
		// if the mailbox don't exist create it
		if (!$mailbox) {
			$mailbox = $inst->_mailboxTable->createRow (array(
			'user_id'=>$userId,
			'name'=>$name,
			'type'=>$type));
			$mailbox->save();			
		}
		
		return $mailbox;
	}
	
	/**
	 *  Gets the number of unread messages
	 *  
	 * @param $userId - if not id specified then gets the inbox of the current user
	 * @return int
	 */
	public static function getUsersUnreadMessages ($userId = null)
	{	
		if (empty($userId)) {
			$user = Tg_Auth::getAuthenticatedUser();
			$userId = $user->id;
		}
			
		$inbox = self::getUsersMailbox ($userId, Tgx_Messaging_Factory::MAILBOX_TYPE_INBOX, 'Inbox');
		return $inbox->unreadMessages ();
		
	}
}