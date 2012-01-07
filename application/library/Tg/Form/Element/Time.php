<?php
class Tg_Form_Element_Time extends Zend_Form_Element_Xhtml
{
    protected $_dateFormat = '%hours%:%minutes%';
    protected $_minutes;
    protected $_hours;

    public function __construct($spec, $options = null)
    {
        $this->addPrefixPath(
            'Tg_Form_Decorator',
            'Tg/Form/Decorator',
            'decorator'
        );
        parent::__construct($spec, $options);
    }
    
    public function loadDefaultDecorators()
    {
        if ($this->loadDefaultDecoratorsIsDisabled()) {
            return;
        }
 
        $decorators = $this->getDecorators();
        if (empty($decorators)) {
            $this->addDecorator('Time')
                 ->addDecorator('Errors')
                 ->addDecorator('Description', array(
                     'tag'   => 'p',
                     'class' => 'description'
                 ))
                 ->addDecorator('HtmlTag', array(
                     'tag' => 'dd',
                     'id'  => $this->getName() . '-element'
                 ))
                 ->addDecorator('Label', array('tag' => 'dt'));
        }
    }
    
    public function setMinutes($value)
    {
        $this->_minutes = (int) $value;
        return $this;
    }
 
    public function getMinutes()
    {
        return $this->_minutes;
    }
 
    public function setHours($value)
    {
        $this->_hours = (int) $value;
        return $this;
    }
 
    public function getHours()
    {
        return $this->_hours;
    }
 
    public function setValue($value)
    {
        if (is_int($value)) {
            $this->setMinutes(date('i', $value))
                 ->setHours(date('H', $value));
        } elseif (is_string($value)) {
            $date = strtotime($value);
            $this->setMinutes(date('i', $date))
                 ->setHours(date('H', $date));
        } elseif (is_array($value) && (isset($value['minutes']) && isset($value['hours']))) {
            $this->setMinutes($value['minutes'])
                 ->setHours($value['hours']);
        } else {
            $this->setMinutes('00')
                 ->setHours('9');
        }
 
        return $this;
    }
 
    public function getValue()
    {
        return sprintf('%02d:%02d', $this->getHours(), $this->getMinutes());
    }
}