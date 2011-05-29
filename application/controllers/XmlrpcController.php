<?php

class XmlrpcController extends Zend_Controller_Action
{

    public function init()
    {
    }

    public function indexAction()
    {
    }

    public function serverAction()
    {
		$this->getHelper('viewRenderer')->setNoRender();

        $server = new Zend_XmlRpc_Server();
        $server->setClass('Application_Model_Palestras', 'palestra'); 
        echo $server->handle();
    }


    public function clientAction()
    {
        $lista = array();
        $pesquisa = array();
        $form = new Application_Form_Pesquisar();


        $client = new Zend_XmlRpc_Client("http://".$_SERVER["HTTP_HOST"]."/xmlrpc/server");
 
        $lista = $client->call('palestra.listarTodas');
        if($_POST['chave'] != ""){
            $pesquisa = $client->call('palestra.pesquisar',array($_POST['chave']));
        }


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



