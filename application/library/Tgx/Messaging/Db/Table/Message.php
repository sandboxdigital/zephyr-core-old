<?php
/**
 * Tg Framework 
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
 * @copyright  Copyright (c) 2009 Thomas Garrood (http://www.garrood.com)
 * @license    New BSD License
 */

class Tgx_Messaging_Db_Table_Message extends Tg_Db_Table
{
	protected $_name = 'mailbox_message';
	protected $_rowClass = 'Tgx_Messaging_Db_Message';
	
    protected $_referenceMap    = array(
        'Tgx_Messaging_Db_Table_Mailbox' => array(
            'columns'           => array('mailbox_id'),
            'refTableClass'     => 'Tgx_Messaging_Db_Table_Mailbox',
            'refColumns'        => array('id')
        )
    );
    
    public function getMailboxMessages ($mailboxId)
    {
		$select = $this->select()
			->setIntegrityCheck(false)
			->from($this->_name)
			->joinLeft(
				array('from_user'=>'user'),'mailbox_message.from_user_id=from_user.id',
				array (
				'fromUserId'=>'from_user.id'
				,'fromUserFirstName'=>'from_user.firstname'
				,'fromUserLastName'=>'from_user.lastname'
				))
			->joinInner(
				array('to_user'=>'user'),'mailbox_message.to_user_id=to_user.id',
				array (
				'toUserId'=>'from_user.id'
				,'toUserFirstName'=>'from_user.firstname'
				,'toUserLastName'=>'from_user.lastname'
				))
			->where('status!="deleted"')
			->where('mailbox_id=?',$mailboxId)
			->order('created_at DESC');
			
		return $this->fetchAll($select);
    }
    
    public function getUnreadMessages ($mailboxId)
    {
		$select = $this->select()
			->setIntegrityCheck(false)
			->from($this->_name)
			->joinLeft(
				array('from_user'=>'user'),'mailbox_message.from_user_id=from_user.id',
				array (
				'fromUserId'=>'from_user.id'
				,'fromUserFirstName'=>'from_user.firstname'
				,'fromUserLastName'=>'from_user.lastname'
				))
			->joinInner(
				array('to_user'=>'user'),'mailbox_message.to_user_id=to_user.id',
				array (
				'toUserId'=>'from_user.id'
				,'toUserFirstName'=>'from_user.firstname'
				,'toUserLastName'=>'from_user.lastname'
				))
			->where('mailbox_id=?',$mailboxId)
			->where('status=?','unread')
			->order('created_at DESC');
			
		return $this->fetchAll($select);
    }
    
    /**
     * Gets a message - only user who owns mailbox can get the message
     * @param $id
     * @param $userId
     * @return unknown_type
     */
    public function getMessage ($id, $userId)
    {
		$select = $this->select()
			->setIntegrityCheck(false)
			->from($this->_name)
			->joinLeft(
				array('from_user'=>'user'),'mailbox_message.from_user_id=from_user.id',
				array (
				'fromUserId'=>'from_user.id'
				,'fromUserFirstName'=>'from_user.firstname'
				,'fromUserLastName'=>'from_user.lastname'
				))
			->joinInner(
				array('to_user'=>'user'),'mailbox_message.to_user_id=to_user.id',
				array (
				'toUserId'=>'from_user.id'
				,'toUserFirstName'=>'from_user.firstname'
				,'toUserLastName'=>'from_user.lastname'
				))
			->joinInner(
				'mailbox','mailbox_message.mailbox_id=mailbox.id',array('mailboxId'=>'mailbox.id'))
			->where('mailbox.user_id=?',$userId)
			->where('mailbox_message.id=?',$id);
			
		return $this->fetchRow($select);
    }
    
    /**
     * Get all messages including deleted
     * @param $id
     * @return unknown_type
     */
    public function getAllMessages ($mailboxId)
    {
		$select = $this->select()
			->setIntegrityCheck(false)
			->from($this->_name)
			->joinLeft(
				array('from_user'=>'user'),'mailbox_message.from_user_id=from_user.id',
				array (
				'fromUserId'=>'from_user.id'
				,'fromUserFirstName'=>'from_user.firstname'
				,'fromUserLastName'=>'from_user.lastname'
				))
			->joinInner(
				array('to_user'=>'user'),'mailbox_message.to_user_id=to_user.id',
				array (
				'toUserId'=>'from_user.id'
				,'toUserFirstName'=>'from_user.firstname'
				,'toUserLastName'=>'from_user.lastname'
				))
			->where('mailbox_id=?',$mailboxId)
			->order('created_at DESC');
			
		return $this->fetchAll($select);
    }
}