<?php

class AtomController extends Zend_Controller_Action
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
        $feed = new Zend_Feed_Writer_Feed;
        

        $feed->setTitle('Webservises Feed ATOM - Palestras');
        $feed->setLink('http://'.$_SERVER["HTTP_HOST"]); 
        $feed->setFeedLink('http://'.$_SERVER["HTTP_HOST"].'/atom/server','atom');
        $feed->addAuthor(array(
             'name'  => 'Zed',
             'email' => 'zedmsater@gmail.com',
             'uri'   => 'http://uzed.com.br',
        ));
        $feed->setDateModified(time());

        $this->_createEntry($feed, $palestra->listarTodas());

        echo $feed->export('atom');

    }

    public function clientAction()
    {
        $feed = new Zend_Feed_Atom('http://'.$_SERVER["HTTP_HOST"].'/atom/server');
        
        $this->view->feed = $feed;
    }

    protected function _createEntry($feed,$lista)
    {
        foreach($lista as $key=>$val)
        {
            $entry = $feed->createEntry();
            $entry->setTitle('Curso - '.$val);
            $entry->setLink('http://'.$_SERVER["HTTP_HOST"].'/curso/id/'.$key);
            $entry->setDateModified(time());
            $entry->setDateCreated(time());
            $entry->setDescription('Descrição do curso '.$val);
            $entry->setContent($val);
            $feed->addEntry($entry);
        }
    }

}



