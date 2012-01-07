<?php 
class Tg_Form_Decorator_Time extends Zend_Form_Decorator_Abstract
{
    public function render($content)
    {
        $element = $this->getElement();
        if (!$element instanceof Tg_Form_Element_Time) {
            // only want to render Date elements
            return $content;
        }
 
        $view = $element->getView();
        if (!$view instanceof Zend_View_Interface) {
            // using view helpers, so do nothing if no view present
            return $content;
        }
 
        $hours   = $element->getHours();
        $minutes = $element->getMinutes();
        $name  = $element->getFullyQualifiedName();
 
        $params = array(
        	'class' => 'tiny'
        );
        
        
        $minOptions = array (
        	'00'=>'00',
	        '05'=>'05',
	        '10'=>'10',
	        '15'=>'15',
	        '20'=>'20',
	        '25'=>'25',
	        '30'=>'30',
	        '35'=>'35',
	        '40'=>'40',
	        '45'=>'45',
	        '50'=>'50',
	        '55'=>'55');
        
        $hourOptions = array (
        	'0',
        	'1',
        	'2',
        	'3',
        	'4',
        	'5',
        	'6',
        	'7',
        	'8',
        	'9',
        	'10',
        	'11',
        	'12',
        	'13','14','15','16','17','18','19','20','21','22','23');
        
        /*
        $hourOptions = array (
        	'12 am',
        	'1 am',
        	'2 am',
        	'3 am',
        	'4 am',
        	'5 am',
        	'6 am',
        	'7 am',
        	'8 am',
        	'9 am',
        	'10 am',
        	'11 am',
        	'12 pm',
        	'1 pm','2 pm','3 pm','4 pm','5 pm','6 pm','7 pm','8 pm','9 pm','10 pm','11 pm');*/
 
        $markup = $view->formSelect($name . '[hours]', $hours, $params, $hourOptions)
                . ' : ' . $view->formSelect($name . '[minutes]', $minutes, $params, $minOptions);
 
        switch ($this->getPlacement()) {
            case self::PREPEND:
                return $markup . $this->getSeparator() . $content;
            case self::APPEND:
            default:
                return $content . $this->getSeparator() . $markup;
        }
    }
}
?>