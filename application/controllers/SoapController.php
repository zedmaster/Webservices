<?php

class SoapController extends Zend_Controller_Action
{

    public function init()
    {
        /* Initialize action controller here */
    }

    public function indexAction()
    {
        // action body
    }

	public function serverAction()
	{
		$this->getHelper('viewRenderer')->setNoRender();


		if(isset($_GET['wsdl'])) {
			//** Cria o arquivo WSDL automaticamente
			$autodiscover = new Zend_Soap_AutoDiscover();
			$autodiscover->setClass('Application_Model_Palestras');
			$autodiscover->handle();
			return;
		}


		$server = new Zend_Soap_Server();

		// Vincula a  classe no Soap Server
		$server->setClass('Application_Model_Palestras');

		// vincula o obeto inicializado no Soap Server
		$server->setObject(new Application_Model_Palestras());

		$server->handle();
	}
}

