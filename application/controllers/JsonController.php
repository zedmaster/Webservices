<?php

class JsonController extends Zend_Controller_Action
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
        $this->_helper->layout->disableLayout();

        $server = new Zend_Json_Server();
        $server->setClass('Application_Model_Palestras');


        if ('GET' == $_SERVER['REQUEST_METHOD']) {
            $server->setTarget('/json/server')
            ->setEnvelope(Zend_Json_Server_Smd::ENV_JSONRPC_2);
 
            $smd = $server->getServiceMap();
 
            header('Content-Type: application/json');
            echo $smd;
            return;
        }

        $server->handle();
    }

    public function clientAction()
    {
        $this->view->headScript()->appendFile('/js/json/client.js','text/javascript');
        $form = new Application_Form_Pesquisar();

        $form->populate($_POST);
        $this->view->form = $form;

    }


}



