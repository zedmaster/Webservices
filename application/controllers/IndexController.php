<?php

class IndexController extends Zend_Controller_Action
{

    public function init()
    {
        /* Initialize action controller here */
    }

    public function indexAction()
    {
		// Código para responder ao OpenID
		if($this->_getParam('openid_mode')){
			$this->_forward('handle','provider','default');
		}

	}


}

