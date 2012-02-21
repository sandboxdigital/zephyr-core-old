<?php

class Tg_Form_Element_Number extends Zend_Form_Element_Text
{
    public $helper = 'formNumber';

	public function init()
    {
//        if ($this->isAutoloadFilters())
//        {
//            $this->addFilter('Digits');
//        }
//
//        if ($this->isAutoloadValidators())
//        {
//            $this->addValidator('Digits');
//            $validatorOpts = array_filter(array(
//                'min' => $this->getAttrib('min'),
//                'max' => $this->getAttrib('max'),
//            ));
//            $validator = null;
//            if (2 === count($validatorOpts))
//            {
//                $validator = 'Between';
//            }
//            else if (isset($validatorOpts['min']))
//            {
//                $validator = 'GreaterThan';
//            }
//            else if (isset($validatorOpts['max']))
//            {
//                $validator = 'LessThan';
//            }
//            if (null !== $validator)
//            {
//                $this->addValidator($validator, false, $validatorOpts);
//            }
//        }
        return $this;
    }
}