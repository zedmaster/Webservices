<?php

class ConsumerController extends Zend_Controller_Action
{
    /**
     * indexAction
     *
     * @return void
     */
    public function indexAction()
    {
        $auth = Zend_Auth::getInstance();
        if (!$auth->hasIdentity()) {
            $this->_redirect('/'.$this->_getParam('module').'/consumer/login');
        } else {
            $this->_redirect('/'.$this->_getParam('module').'/consumer/welcome');
        }
    }

    /**
     * welcomeAction
     *
     * @return void
     */
    public function welcomeAction()
    {
        $auth = Zend_Auth::getInstance();
        if (!$auth->hasIdentity()) {
            //$this->_redirect('/default/consumer/login');
        }
        $this->view->user = $auth->getIdentity();
    }

    /**
     * loginAction
     *
     * @return void
     */
    public function loginAction()
    {
        if (isset($_POST['openid_action']) &&
            $_POST['openid_action'] == "login" &&
            !empty($_POST['openid_identifier'])) 
        {
 
            $consumer = new Zend_OpenId_Consumer();
            if (!$consumer->login($_POST['openid_identifier'])) 
            {
                $status = $consumer->getError();
            }
        } else if (isset($_GET['openid_mode'])) 
        {
            if ($_GET['openid_mode'] == "id_res") 
            {
                $consumer = new Zend_OpenId_Consumer();
                if ($consumer->verify($_GET, $id)) {
				    $this->_redirect('/'.$this->_getParam('module').'/consumer/welcome');
                    
                } else {
                    $status = "INVALID " . htmlspecialchars($id);

                }
            } else if ($_GET['openid_mode'] == "cancel") {
                $status = "CANCELLED";
            }
        }
	    $this->view->status .= "$status<br>\n";
    }



    
	/**
	 * logoutAction
	 *
	 * @return void
	 */
	public function logoutAction()
	{
		Zend_Auth::getInstance()->clearIdentity();
		$this->_redirect('/'.$this->_getParam('module').'/consumer/index');
	}
}
