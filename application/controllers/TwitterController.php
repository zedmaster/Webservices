<?php

class TwitterController extends Zend_Controller_Action
{

    public function init()
    {
    }

    public function indexAction()
    {
    }

    public function clientAction()
    {
        $access = new Zend_Oauth_Token_Access();
        $access->setToken('273546082-kXkYqZ9BNzhFMWWRfEVanhnvvFtMWz4Y5bv1cspU')
               ->setTokenSecret('w82bUMIyJQfua1wfZMbsjTrRLmGCimlj08JAvQiTvQ');
        $params = array(
                'accessToken' => $access,
                'consumerKey' => 'D3yhsznSeN8SEKR5SmjamA',
                'consumerSecret' => 'tNq875fYSTKu6CmG8U2ilnDgX4U6N3SSwtkOHoBmnA'
                );
        $twitter = new Zend_Service_Twitter($params);

        //** Busca followers 
        $followers = $twitter->user->followers();
        $followers = (array)$followers->getIterator();
        $followers = $followers['user'];
        $this->view->followers = $followers;

        //** Busca timeline
        $timeline = $twitter->status->friendsTimeline();
        $timeline = (array)$timeline->getIterator();
        $timeline = $timeline['status'];
        $this->view->timeline = $timeline;
    }


}



