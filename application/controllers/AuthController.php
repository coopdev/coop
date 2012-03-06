<?php

class AuthController extends Zend_Controller_Action
{

    public function init()
    {
        /* Initialize action controller here */
    }

    public function indexAction()
    {
        // action body
    }

    public function casAction()
    {
        // action body
        $auth = Zend_Auth::getInstance();

        $config_cas = new Zend_Config_Ini(APPLICATION_PATH.'/configs/cas.ini', 'cas');
        //$adapter = new Zend_Auth_Adapter_Cas($config_cas->params);
        $adapter = new My_Auth_Adapter_Cas($config_cas->params);
        //$adapter->setService($service); // ASK ABOUT $service. IT WORKS FOR THE SAMPLE APP BUT IS UNDEFINED FOR THIS APP.
        $adapter->setLoginUrl();
        //die($adapter->getLoginUrl());
        

        if(isset($_GET['ticket'])) {
            $adapter->setTicket($_GET['ticket']);
            //$adapter->setService($service);
            $adapter->setQueryParams(array($adapter->getTicket(), $adapter->getService()));
            //die(var_dump($adapter->getQueryParams()));
            //die($adapter->getTicket());
        }

        if(!$auth->hasIdentity() && $adapter->hasTicket()) {
            $result = $auth->authenticate($adapter);
            //die(var_dump($result));
            if(!$result->isValid()) {
               //die($result->getMessages());
                $this->view->messages = $result->getMessages();
                return;
            }
        }

        // Logout if requested
        if(isset($_GET['logout'])) {

            $auth->clearIdentity();

            // Specify the landing URL to hit after logout
            $local_service = $config_cas->params->local_service;  //get the website URL
            $landingUrl = $adapter->getUrl().'/logout?service='.$local_service.'/auth/logout';

            $adapter->setLogoutUrl($landingUrl);
            Zend_Session::destroy(true);

            $this->_redirect($adapter->getLogoutUrl());
            //$this->_redirect('http://localhost/zf-tutorial/public/auth/login');
        }

        // Send to CAS for authentication
        if(!$auth->hasIdentity()) {
            $this->_redirect($adapter->getLoginUrl());
        } else {
            //$_SESSION['uhinfo'] = $result->getMessages();
            $coopSess = new Zend_Session_Namespace('coop');
            $coopSess->uhinfo = $result->getMessages();
            //$uhinfo = $result->getMessages();
            //die(var_dump($uhinfo->all));
            //die($_SESSION['uhinfo']['user']);
            $this->_redirect($local_service."/pages/home");
        }        
    }

    public function loginAction()
    {
        // action body
    }

    public function logoutAction()
    {
       $this->render('logout');
    }


}







