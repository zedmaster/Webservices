<?php

class EbayfindingController extends Zend_Controller_Action
{

    public function init()
    {
    }

    public function indexAction()
    {
    }

    public function clientAction()
    {
        $finding  = new Zend_Service_Ebay_Finding('ZedNet3dd-6a80-411b-9786-02c3781bae4');
        $response = $finding->findItemsByKeywords('MSX');
        $this->view->resposta = $response->searchResult->item;
    }


}



