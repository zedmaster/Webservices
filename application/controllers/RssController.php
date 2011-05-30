<?php

class RssController extends Zend_Controller_Action
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

        $palestra = new Application_Model_Palestras(); 
        $feed = new Zend_Feed_Writer_Feed();
        

        $feed->setTitle('Webservises Feed RSS - Palestras');
        $feed->setLink('http://'.$_SERVER["HTTP_HOST"]); 
        $feed->setFeedLink('http://'.$_SERVER["HTTP_HOST"].'/rss/server','rss');
        $feed->addAuthor(array(
             'name'  => 'Zed',
             'email' => 'zedmsater@gmail.com',
             'uri'   => 'http://uzed.com.br',
        ));
        $feed->setDateModified(time());
        $feed->setDescription('RSS FEEDS');

        $this->_createEntry($feed, $palestra->listarTodas());


        echo $feed->export('rss');

    }

    public function clientAction()
    {
        $feed = new Zend_Feed_Rss('http://'.$_SERVER["HTTP_HOST"].'/rss/server');
        
        $this->view->feed = $feed;
    }

    protected function _createEntry($feed, $lista)
    {
        foreach($lista as $key=>$val)
        {
            $entry = $feed->createEntry();
            $entry->addAuthor(array('name' => 'Zed'));
            $entry->setTitle('Curso - '.$val);
            $entry->setLink('http://'.$_SERVER["HTTP_HOST"].'/curso/key/'.$key);
            $entry->setDateModified(time());
            $entry->setDateCreated(time());
            $entry->setDescription('Descrição - '.$val);
            $feed->addEntry($entry);
        }
        
    }


}
