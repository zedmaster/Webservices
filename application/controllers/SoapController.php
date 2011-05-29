<?php

class SoapController extends Zend_Controller_Action
{
    public $wsdl;

    public function init()
    {
        //** Monta o link do WSDL
        $this->wsdl = "http://".$_SERVER["HTTP_HOST"]."/soap/server?wsdl";
    }

    public function indexAction()
    {
        // action body
    }

    public function serverAction()
    {
		$this->getHelper('viewRenderer')->setNoRender();
        $this->_helper->layout->disableLayout();

		if(isset($_GET['wsdl'])) {
			//** Cria o arquivo WSDL automaticamente
			$autodiscover = new Zend_Soap_AutoDiscover();
			$autodiscover->setClass('Application_Model_Palestras');
			$autodiscover->handle();
			return;
		}


		$server = new Zend_Soap_Server($this->wsdl);

		// Vincula a  classe no Soap Server
		$server->setClass('Application_Model_Palestras');

		// vincula o obeto inicializado no Soap Server
		$server->setObject(new Application_Model_Palestras());

		$server->handle();
    }

    public function clientAction()
    {
        $lista = array();
        $pesquisa = array();
        $form = new Application_Form_Pesquisar();

        $client = new Zend_Soap_Client($this->wsdl);
        $lista = $client->listarTodas();
        $pesquisa = $client->pesquisar($_POST['chave']);

        if($this->getRequest()->isPost() && !$pesquisa)
        {
            $pesquisa = array("Curso não encontrado.");
        }


        $this->view->lista = $lista;
        $this->view->pesquisa = $pesquisa;
        $form->populate($_POST);
        $this->view->form = $form;
    }


}



