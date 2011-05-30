<?php
/**
 * @category   Zend
 * @package    Zend_OpenId
 * @subpackage Demos
 * @uses       Zend_Controller_Action
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class ConsumerController extends Zend_Controller_Action
{
	public function init()
	{
    	$this->_helper->layout->disableLayout();
	}

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
            $this->_redirect('index/login');
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
        $this->view->status = "";
        if (($this->_request->isPost() &&
             $this->_request->getPost('openid_action') == 'login' &&
             $this->_request->getPost('openid_identifier', '') !== '') ||
            ($this->_request->isPost() &&
             $this->_request->getPost('openid_mode') !== null) ||
            (!$this->_request->isPost() &&
             $this->_request->getQuery('openid_mode') != null)) 
		{

			$client = new Zend_Http_Client($this->_request->getPost('openid_identifier'));

			//Extensions
			$sreg = new Zend_OpenId_Extension_Sreg(array(
														 'nickname'=>true,
														 'email'=>false,
														 'fullname'=>false,
														 'dob'=>false,
														 'gender'=>false,
														 'postcode'=>false,
														 'country'=>false,
														 'language'=>false,
														 'timezone'=>false
														 ), null, 1.1);


			$openId = new Zend_Auth_Adapter_OpenId($this->_request->getPost('openid_identifier'));
			$openId->setHttpClient($client);
			$openId->setExtensions($sreg);

			$auth = Zend_Auth::getInstance();
			$result = $auth->authenticate($openId);

			if ($result->isValid()) {
				//$session = $auth->getStorage();
				//$session->write($sreg->getProperties());
				$this->_redirect('/'.$this->_getParam('module').'/consumer/welcome');
			} else {
				$auth->clearIdentity();
				foreach ($result->getMessages() as $message) {
					$this->view->status .= "$message<br>\n";
				}
			}
		}
		$this->render();
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
