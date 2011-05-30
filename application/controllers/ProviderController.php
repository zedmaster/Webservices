<?php

class ProviderController extends Zend_Controller_Action
{
    public $session;
    public $host;
    public $server;


    /**
     *
     *
     */
    public function init()
    {
		$this->session = new Zend_Session_Namespace("opeinid.server");
		$this->host = "http://".$_SERVER["HTTP_HOST"] ;
		$this->server = new Zend_OpenId_Provider($this->host.'/default/provider/login',
												 $this->host.'/default/provider/trust');

        if (isset($this->session->id)) {
            $this->view->id = $this->session->id;
        }
        if (isset($this->session->error)) {
            $this->view->error = $this->session->error;
            unset($this->session->error);
        }

    }

    /**
     *
     *
     */
	public function handleAction()
	{
		if($this->_getParam('openid_mode'))
		{
			$this->getHelper('viewRenderer')->setNoRender();
            $this->_helper->layout->disableLayout();


			echo $this->server->handle($this->_getAllParams(), new Zend_OpenId_Extension_Sreg());
			return;
		}
		$this->_redirect('/default/provider/');
	}

    /**
     *
     *
     */
	public function userAction()
	{
		$this->getHelper('viewRenderer')->setNoRender();
        $this->_helper->layout->disableLayout();


		if($this->_getParam('openid_mode')){
			$this->_forward('handle','provider','default');
		}

		if ($this->_getParam('openid')) {
			$url = $this->host . '/default/provider/user/openid/' . $this->_getParam("openid");
			if ($this->server->hasUser($url)) {
				$this->view->server = $this->host;
				$this->view->name = $this->_getParam("openid");
				$this->render('identity');
				return;
			}
		} else if ($this->_getParam('openid2')) {
			$url = $this->host . '/default/provider/user/openid2/' . $this->_getParam("openid2");
			if ($this->server->hasUser($url)) {
				$this->view->server = $this->host;
				$this->view->name = $this->_getParam("openid2");
				$this->render('identity2');
				return;
			}
		}  

		echo "Forbiden";
	}

    /**
     *
     *
     */
	public function indexAction()
	{	
		if($this->_getParam('openid_mode'))
		{
			$this->_forward('handle','provider','default',$this->_getAllParams());
			return;
		}

		if ($this->server->getLoggedInUser() !== false) {
			$this->view->url = $this->server->getLoggedInUser();
			if ($this->server->hasUser($this->view->url)) {
				$sites = $this->server->getTrustedSites();
				$s = "";
				foreach ($sites as $site => $trusted) {
					if (is_bool($trusted) || is_array($trusted)) {
						$s .= $this->trust_form($site, $trusted);
					}
				}
				if (empty($s)) {
					$s = "<tr><td>None</td></tr>";
				}
				$this->view->sites = $s;
				$this->render('profile');
				return;
			}
		} else {
			//$this->_redirect('/default/provider/login');
			$this->_forward('login','provider','default',$this->_getAllParams());
		}
	}

    /**
     *
     *
     */
	public function registerAction()
	{
		if ($this->_getParam('openid_name') &&	$this->_getParam('openid_password') &&	$this->_getParam('openid_password2')) 
		{
			$url = $this->host. '/default/provider/user/openid/' . $this->_getParam('openid_name');
			if ($this->_getParam('openid_password') != $this->_getParam('openid_password2'))
			{
				$this->view->name = $this->_getParam('openid_name');
				$this->view->error = 'Password mismatch.';

			} else if ($this->server->register($url, $this->_getParam('openid_password'))) {
				$this->_forward('registrationcomplete', 'provider','default',$_POST);
			} else {
				$this->view->error = 'Registration failed. Try another name.';
			}

		}
	}

    /**
     *
     *
     */
	public function registrationcompleteAction()
	{
		$this->view->name = $this->_getParam('openid_name');
		$this->view->url = $this->host . '/default/provider/user/openid/' . $this->view->name;
		$this->view->url2 = $this->host . '/default/provider/user/openid2/' . $this->view->name;
	}


    /**
     *
     *
     */
	public function loginAction()
	{
		if ($this->_getParam('openid_url') && $this->_getParam('openid_password')) 
		{
			if (!$this->server->login($this->_getParam('openid_url'),	$this->_getParam('openid_password'))) 
			{
				$this->session->error = 'Wrong identity/password!';
				$this->session->id = $this->_getParam('openid_url');
				Zend_OpenId::redirect($this->host.'/default/provider/login', $_GET);
				return;
			}
			unset($_GET['openid_action']);
			Zend_OpenId::redirect($this->host.'/default/provider/index', $_GET);
			return;
		}else{
			if (isset($_GET['openid_identity'])) {
				$this->view->id = $_GET['openid_identity'];
				$this->view->ro = true;
			}
		} 

	}

    /**
     *
     *
     */
	public function logoutAction()
	{
		$this->server->logout();
		$this->_forward('index','provider','default');
	}

    /**
     *
     *
     */
	public function trustAction()
	{
		$this->getHelper('viewRenderer')->setNoRender();
        $this->_helper->layout->disableLayout();

		//var_dump($_GET);
		//var_dump($_POST);

		if ($_SERVER["REQUEST_METHOD"] == "GET") 
		{
			if ($this->server->getLoggedInUser() !== false) {
				$this->view->site = $this->server->getSiteRoot($_GET);
				$this->view->url = $this->server->getLoggedInUser();
				$sreg = new Zend_OpenId_Extension_Sreg();
				$sreg->parseRequest($_GET);
				$this->view->sreg = $this->sreg_form($sreg);
				if ($this->server->hasUser($this->view->url)) {
					$this->render('trust');
					return;
				}
			}   
		}else{
			if (isset($_GET['openid_return_to'])) {
				$sreg = new Zend_OpenId_Extension_Sreg();
				$sreg->parseResponse($_POST);
				if (isset($_POST['allow'])) {
					if (isset($_POST['forever'])) {
						$this->server->allowSite($this->server->getSiteRoot($_GET), $sreg);
					}
					unset($_GET['openid_action']);
					$this->server->respondToConsumer($_GET, $sreg);
				} else if (isset($_POST['deny'])) {
					if (isset($_POST['forever'])) {
						$this->server->denySite($this->server->getSiteRoot($_GET));
					}
					Zend_OpenId::redirect($_GET['openid_return_to'], array('openid.mode'=>'cancel'));
				}
			} else if (isset($_POST['allow'])) {
				$this->server->allowSite($_POST['site']);
				header('Location: ' . $_SERVER['PHP_SELF']);
				exit;
			} else if (isset($_POST['deny'])) {
				$this->server->denySite($_POST['site']);
				header('Location: ' . $_SERVER['PHP_SELF']);
				exit;
			} else if (isset($_POST['del'])) {
				$this->server->delSite($_POST['site']);
				header('Location: ' . $_SERVER['PHP_SELF']);
				exit;
			}
		}




	}



	public function trust_form($site, $trusted) {
		if (is_array($trusted)) {
			$str = "";
			if (isset($trusted['Zend_OpenId_Extension_Sreg'])) {
				$trusted = $trusted['Zend_OpenId_Extension_Sreg'];
				foreach ($trusted as $key => $val) {
					$str .= "$key:\"$val\";";
				}
			}
			$trusted = true;
		}
		$s = "<form method=\"POST\" action=\"/default/provider/trust\" >"
			. '<tr><td>'
			. '<input type="hidden" name="openid_action" value="trust">'
			. '<input type="hidden" name="site" value="' . $site . '">'
			. $site
			//       . '</td><td>'
			//     . ($trusted ? 'allowed' : 'denied')
			. '</td><td>'
			. ($trusted ?
			   '<input type="submit" style="width:100px" name="deny" value="Deny">' :
			   '<input type="submit" style="width:100px" name="allow" value="Allow">')
			. '</td><td>'
			.  '<input type="submit" style="width:100px" name="del" value="Del">'
			. '</td><td>'.$str.'</td></tr>'
			. '</form>';
		return $s;
	}


	public function sreg_form(Zend_OpenId_Extension_Sreg $sreg)
	{
		$s = "";
		$props = $sreg->getProperties();
		if (is_array($props) && count($props) > 0) {
			$s = 'It also requests additinal information about you';
			$s .= ' (fields marked by <u>*</u> are required)<br>';
			$s .= '<table border="0" cellspacing="2" cellpadding="2">';
			foreach ($props as $prop => $val) {
				if ($val) {
					$s .= '<tr><td><u>'.$prop.':*</u></td>';
				} else {
					$s .= '<tr><td>'.$prop.':</u></td>';
				}
				$value = "";
				$s .= '<td><input type="text" name="openid.sreg.'.$prop.'" value="'.$value.'"></td></tr>';
			}
			$s .= '</table><br>';
			$policy = $sreg->getPolicyUrl();
			if (!empty($policy)) {
				$s .= 'The private policy can be found at <a href="'.$policy.'">'.$policy.'</a>.<br>';
			}
		}
		return $s;
	}
}

