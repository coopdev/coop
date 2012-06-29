<?php

class IndexController extends Zend_Controller_Action
{

    public function init()
    {
        $req = $this->getRequest();
        //die($req->getActionName());
        //die($req->getControllerName());
        $this->_helper->redirector('login','pages');
    }

    public function indexAction()
    {
        //die('hi');
        // action body
    }

    public function testAction()
    {
        // action body
    }

}







