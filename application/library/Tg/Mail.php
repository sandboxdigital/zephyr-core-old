<?php

class Tg_Mail extends Zend_Mail
{

	protected $_config = null;
	public $subject = null;
	public $variables = null;
	public $content = null;
	public $body = null;
	public $to = null;
	public $toName = null;
	public $from = null;
	public $fromName = null;
	public $template = null;
	
	private $_defaultConfig = array ();

	public function __construct($charset = null) 
	{
		parent::__construct($charset);
		
		$config = Zend_Registry::get('config');
		
		if (isset($config['mail']))
			$this->_config  = $config['mail']+$this->_defaultConfig;
		else
			$this->_config  = $this->_defaultConfig;
		
		if(isset($this->_config['host'])) {
			if(isset($this->_config['user']) && isset($this->_config['pass'])) {
				$_config = array('auth' => 'login', 'username' => $this->_config['user'], 'password' => $this->_config['pass']);
			}
			else {
				$_config = array();
			}
			$tr = new Zend_Mail_Transport_Smtp($this->_config['host'], $_config);
		}
		else {
			if(isset($this->_config['from'])) {
				$tr = new Zend_Mail_Transport_Sendmail('-f'.$this->_config['from']);
			}
			else {
				$tr = new Zend_Mail_Transport_Sendmail();
			}
		}
		$this->_charset = 'iso-8859-1';
		Zend_Mail::setDefaultTransport($tr);
	
		if($this->_config['to']) {
			$this->to = $this->_config['to'];
		}

		if($this->_config['from']) {
			$this->from = $this->_config['from'];
		}

		if($this->_config['fromName']) {
			$this->fromName = $this->_config['fromName'];
		}
		
		if ($this->_config['defaultTemplate'])
			$this->template = $this->_config['defaultTemplate'];
    }
    
    public function getOption ($name)
    {
    	if(isset($this->_config[$name]))
			return $this->_config[$name];
    }

	public function send() 
	{
		$this->_build ();
		
//		try {
			parent::send();
//		}
//		catch (Zend_Exception $e) {
//			$_config = Zend_Registry::get('config');
//			if($_config->exceptions == "true") {
//				Zend_Registry::get('logger')->err($e);
//			}
//		}
	}

	protected function _build() 
	{
		if ($this->from == null)
			throw new Zend_Exception ('Mail - no from set');
		
		if ($this->from == null)
			throw new Zend_Exception ('Mail - no fromName set');
		
		if ($this->to == null)
			throw new Zend_Exception ('Mail - no to set');
		
		if ($this->body == null && $this->template == null)
			throw new Zend_Exception ('Mail - no body or template set');
			
		// subject
		if($this->content != null && $this->subject == null) {

			if(isset($this->_config['subjects'][$this->content])) {
				$subject = $this->_config['subjects'][$this->content];
				if(is_array($this->variables)) {
					foreach($this->variables as $key=>$val) {
						$subject = str_replace('{'.$key.'}', $val, $subject);
					}
				}
			}
			else {
				$subject = 'Subject Not Found';
			}

			$this->subject = $subject;
		}
		
		// body - if there is a template set use that otherwise use $this->body
		if ($this->template) {
			$this->body = $this->buildBodyFromTemplate ($this->template, $this->variables);
		} 
		
		$html = $this->body;
			
		// to - people we are sending email to
		if(is_array($this->to)) {
			foreach($this->to as $to) {
				$this->addTo($to, $to);
			}
		}
		else {
			$this->addTo($this->to, $this->toName);
		}
		
		// from 
		$this->setFrom($this->from, $this->fromName);
		$this->setSubject($this->subject);
		
		// set content
		$this->setBodyText('This is an HTML formatted email. Please view this email with a HTML capable email program.');
		$this->setBodyHtml($html);
	}
	
	function buildBodyFromTemplate ($template, $variables)
	{
		$html = '';
		$view = new Zend_View();
		$view->addScriptPath($this->_config['templatePath']);

		$page = Tg_Site::getInstance()->getCurrentPage();
		if ($page)
		{
			$theme = $page->getTheme();
			$view->addScriptPath(Zeph_Config::getPath('%PATH_PUBLIC%/themes/'.$theme->folder.'/views/emails'));
		}
		
		if (isset($view->content))
			$view->content = $this->content;

		$config = array('noController' => true, 'neverRender' => true);

		$viewRenderer = new Zend_Controller_Action_Helper_ViewRenderer($view, $config);
		$viewRenderer->render($template, 'body', true);

		$html = $viewRenderer->getResponse()->getBody();
		$viewRenderer->getResponse()->clearBody();
		
		// do some simple variable substitution
		if (isset($variables)) {
			if(is_array($variables)) {
				foreach($variables as $key=>$val) {
					$html = str_replace('{'.$key.'}', $val, $html);
				}
			}
		}
		
		return $html;
	}
}