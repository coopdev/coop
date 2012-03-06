<?php

class PagesController extends Zend_Controller_Action
{

    public function init()
    {
        /* Initialize action controller here */
    }

    public function indexAction()
    {
        // action body
    }

    public function homeAction()
    {
        // Instantiate session. Must be instantiated to access it. First instantiated in Bootstrap.ini
        // This is equivalent to $_SESSION['coop']. $coopSess->uhinfo is equivalent to $_SESSION['coop']['uhinfo']
        $coopSess = new Zend_Session_Namespace('coop');
        $this->view->uhinfo = $coopSess->uhinfo;

        /**
         *  Bottom three lines used as a test: first gets current instance of authenticated user.
         *  Next gets the stored information of the current authenticated user 
         *  (in this case "uhinfo" because that was written to storage during CAS authentication.)
         */
        //$auth = Zend_Auth::getInstance();
        //$identity = $auth->getStorage()->read();
        //die(var_dump($identity));
    }


}



