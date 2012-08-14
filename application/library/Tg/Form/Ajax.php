<?php


class Tg_Form_Ajax extends Tg_Form
{
	const RETURN_NONAJAX_SUCCESS = 1;
	const RETURN_NONAJAX_FAIL = 2;
	const RETURN_AJAX_SUCCESS = 3;
	const RETURN_AJAX_FAIL = 4;

	public function __construct($options = array())
	{
        if (empty($options['id']))
            $options['id'] = 'ajaxForm'.rand(0,10000);

		parent::__construct($options);
	}

	public function process($request=null)
	{
		if (empty($request))
		{
			$frontController = Zend_Controller_Front::getInstance();
			$request = $frontController->getRequest();
		}

		if($request->isXmlHttpRequest()) {
		    $return = $this->processAjax($request->getPost());
			$valid = $return=='true'?true:false;
			if ($valid)
				$this->onValid();
			else
				$this->onError();
            Zend_Controller_Action_HelperBroker::getStaticHelper('viewRenderer')->setNoRender(true);
			echo $return;
			return $valid ? Tg_Form_Ajax::RETURN_AJAX_SUCCESS:Tg_Form_Ajax::RETURN_AJAX_FAIL;
		} else {
			$valid = parent::process($request);
			return $valid ? Tg_Form_Ajax::RETURN_NONAJAX_SUCCESS:Tg_Form_Ajax::RETURN_NONAJAX_FAIL;
		}
	}

    public function render($v = null)
    {
        $return = parent::render($v);

        return $return;
    }

    /**
     * Process submitted AJAX data
     *
     * Checks if provided $data is valid, via {@link isValidPartial()}. If so,
     * it returns JSON-encoded boolean true. If not, it returns JSON-encoded
     * error messages (as returned by {@link getMessages()}).
     *
     * @param  array $data
     * @return string JSON-encoded boolean true or error messages
     */
    public function processAjax(array $data)
    {
        require_once 'Zend/Json.php';
        if ($this->isValidPartial($data)) {
            return Zend_Json::encode(true);
        }
        $messages = $this->getMessages();

	    foreach ($messages as $elementId => $errors) {
            $element = $this->$elementId;

            if ($element instanceof Zend_Form) {

            } else if ($element instanceof Zend_Form_Element) {

                $label = $element->getLabel();
                if (empty($label)) {
                    $label = $element->getName();
                }
                $label = trim($label);
                if (empty($label)) {
                    $label = '';
                }
                if (null !== ($this->_translator = $element->getTranslator())) {
                    $label = $this->_translator->translate($label);
                }

                $error_msg = '';
                foreach ($errors as $key => $error) {
//                    $error_msg = $view->escape($error);
                    $error_msg = $error;
	                $error_msg = str_replace('%label%', '<span class="label">' . $label . '</span>', $error_msg);
	                $messages[$elementId][$key] = $error_msg;
                }
            }
        }

        return Zend_Json::encode($messages);
    }
}