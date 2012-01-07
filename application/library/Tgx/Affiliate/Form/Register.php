<?php
class Tgx_Affiliate_Form_Register extends Tg_Form {

	public function __construct($options = array())
	{
		parent::__construct($options);
		
		if (isset($options['admin']))
		{
			
					
			$this->addElement('text', 'code', array (
				'label' 	 => 'Affiliate Code',
				'required' 	 => true,
				'filters' 	 => array('StringTrim'),
				'class' 	 => 'text'
			));
					
			unset($options['admin']);			
		}
				
		$this->addElement('text', 'firstname', array (
			'label' 	 => 'Firstname',
			'required' 	 => true,
			'filters' 	 => array('StringTrim'),
			'validators' => array(
				array('StringLength', false, array(3, 50)),
				array('Regex', false, array('/^[a-zA-Z \-\']+$/'))
			),
			'class' 	 => 'text'
		));
		
		$this->addElement('text', 'lastname', array (
			'label' 	 => 'Lastname',
			'required' 	 => true,
			'filters' 	 => array('StringTrim'),
			'validators' => array(
				array('StringLength', false, array(3, 50)),
				array('Regex', false, array('/^[a-zA-Z \-\']+$/'))
			),
			'class' 	 => 'text'
		));

		$this->addElement('text', 'email', array (
			'label' 	 => 'Email Address',
			'required' 	 => true,
			'validators' => array(
				'EmailAddress',
				array('Unique', false, array('Tgx_Affiliate_Db_Table_Affiliate', 'email'))
			),
			'class' 	 => 'text',
			'autocomplete' => 'off'
		));
	
		$this->addElement('password', 'password', array (
			'label' 	 => 'Password',
			'required' 	 => true,
			'filters' 	 => array('StringTrim'),
			'validators' => array(
				array('StringLength', false, array(3, 13))
			),
			'class' 	 => 'text',
			'autocomplete' => 'off'
			
		));

		$this->addElement('password', 'password_confirm', array (
			'label' 	=> 'Confirm Password',
			'required' 	 => true,
			'class' 	 => 'text',
			'validators' => array(
				array('IdenticalToElement', false, array('password'))
			),
			'autocomplete' => 'off'
		));	
	
		$this->addElement('text', 'companyName', array (
			'label' 	 => 'Company Name',
			'required' 	 => true,
			'filters' 	 => array('StringTrim'),
			'validators' => array(
				array('StringLength', false, array(2, 50)),
			),
			'class' 	 => 'text'
		));
	
		$this->addElement('text', 'companyUrl', array (
			'label' 	 => 'Company URL',
			'filters' 	 => array('StringTrim'),
			'validators' => array(
				array('StringLength', false, array(2, 50)),
			),
			'class' 	 => 'text'
		));
	
		$this->addElement('text', 'companyPosition', array (
			'label' 	 => 'Position in company',
			'required' 	 => true,
			'filters' 	 => array('StringTrim'),
			'validators' => array(
				array('StringLength', false, array(3, 50)),
				array('Regex', false, array('/^[a-zA-Z \-\']+$/'))
			),
			'class' 	 => 'text'
		));
	
		$this->addElement('text', 'telephone', array (
			'label' 	 => 'Contact Number',
			'required' 	 => true,
			'filters' 	 => array('StringTrim'),
			'validators' => array(
				array('StringLength', false, array(3, 50)),
				array('Regex', false, array('/^[0-9 \(\)\+]+$/'))
			),
			'class' 	 => 'text'
		));

		$this->addElement('submit', 'register', array (
			'label' => 'Sign up',
			'class' => 'submit'
			));
	}

	public function addElement ($type, $name, $options = array (), $dontChangeDecorators = false)
	{
		parent::addElement ($type, $name, $options);

		if ($type == 'hidden') {
			$element = $this->getElement ($name);
			$element->clearDecorators();
			$element->addDecorator('ViewHelper');
		} else if ($type == 'checkbox') {
			// nothing
			$element = $this->getElement ($name);

			$element->clearDecorators();

			$element
				->addDecorator('ViewHelper')
				->addDecorator('Description', array('tag' => 'span', 'class' => 'description', 'escape' => false, 'class'=>'checkbox'))
				->addDecorator('HtmlTag', array('tag' => 'dd', 'id'  => $element->getName() . '-element', 'class'=>'checkbox'))
				->addDecorator(new Tg_Form_Decorator_Label(array('tag' => 'dt', 'escape' => false, 'class'=>'checkbox')))
			;
		} else if ($type == 'submit' || $type == 'button') {
			// nothing
			$element = $this->getElement ($name);

			$element->clearDecorators();

			$element
			->addDecorator('ViewHelper')
			//				->addDecorator('Description', array('tag' => 'span', 'class' => 'description'))
			->addDecorator('HtmlTag', array('tag' => 'dt',
                             'id'  => $element->getName() . '-element'))
			//                ->addDecorator(new Tg_Form_Decorator_Label(array('tag' => 'dt',
			//                            'escape' => false)));
			;
		} else if (!$dontChangeDecorators){
			$element = $this->getElement ($name);

			$element->clearDecorators();

			$element
			->addDecorator('ViewHelper')
			->addDecorator('Description', array('tag' => 'span', 'class' => 'description', 'escape' => false))
			->addDecorator('HtmlTag', array('tag' => 'dd',
                             'id'  => $element->getName() . '-element'))
			->addDecorator(new Tg_Form_Decorator_Label(array('tag' => 'dt',
                            'escape' => false)));
		}
		return $this;
	}
}