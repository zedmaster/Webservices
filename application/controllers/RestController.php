<?php

class RestController extends Zend_Controller_Action
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

        $server = new Zend_Rest_Server();
        $server->setClass('Application_Model_Palestras');
        $server->handle();
    }

    public function clientAction()
    {
        $lista = array();
        $pesquisa = array();
        $form = new Application_Form_Pesquisar();


        $client = new Zend_Rest_Client("http://".$_SERVER["HTTP_HOST"]."/rest/server");
 
        $lista = $client->listarTodas()->get();
        $lista = (array)$lista->listarTodas;
        unset($lista['status']);

        if($_POST['chave'] != ""){
            $pesquisa = $client->pesquisar($_POST['chave'])->get();
            $pesquisa = (array)$pesquisa->pesquisar;
            unset($pesquisa['status']);
        }

        if($this->getRequest()->isPost() && $pesquisa['response'] == "0" )
        {
            $pesquisa = array("Curso não encontrado.");
        }

        $this->view->lista = $lista;
        $this->view->pesquisa = $pesquisa;
        $form->populate($_POST);
        $this->view->form = $form;

    }


}



