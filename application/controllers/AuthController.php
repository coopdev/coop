<?php

class AuthController extends Zend_Controller_Action
{

    private $auth = null;

    private $config_cas = null;

    private $adapter = null;

    private $coopSess = null;

    private $result = null;

    public function init()
    {
       $this->auth = Zend_Auth::getInstance();
       $this->config_cas = new Zend_Config_Ini(APPLICATION_PATH.'/configs/cas.ini', 'cas');
       $this->adapter = new My_Auth_Adapter_Cas($this->config_cas->params);
       $this->coopSess = new Zend_Session_Namespace('coop');
       
    }


    public function casAction()
    {
        $coopSess = new Zend_Session_Namespace('coop');
        //$adapter->setService($service); // ASK ABOUT $service. IT WORKS FOR THE SAMPLE APP BUT IS UNDEFINED FOR THIS APP.
        $local_service = $this->config_cas->params->local_service;
        $this->adapter->setLoginUrl();
        
        // If user authenticated through CAS, and CAS returned a ticket.
        if(isset($_GET['ticket'])) {
            $this->adapter->setTicket($_GET['ticket']);
            //$this->adapter->setService($service);
            
            // Sets the query params to send to the CAS server for ticket
            // validation. The service ($this->adapter->getService()) is the
            // url the CAS server will redirect to.
            $this->adapter->setQueryParams(
                              array($this->adapter->getTicket(), 
                                    $this->adapter->getService())
                            );
                   
        }
        
        // If the user has a ticket but it hasn't been validated yet 
        // (i.e right after the user authenticates through the CAS server but
        // before the ticket is actually validated).
        if(!$this->auth->hasIdentity() && $this->adapter->hasTicket()) {
           
           // Validate the ticket
            $this->result = $this->auth->authenticate($this->adapter);
            
            if(!$this->result->isValid()) {
               //die($this->result->getMessages());
               $this->view->messages = $this->result->getMessages();
               return;
            }
            if($this->auth->hasIdentity()) {
               $coopSess->uhinfo = $this->result->getMessages();

               $this->_helper->redirector('post-cas');
            }
            //die(var_dump($coopSess->uhinfo,$coopSess->contractStatus,$coopSess->role,$coopSess->inDb));
        }
                
        if(!$this->auth->hasIdentity()) {
           // Send to WebLogin server for authentication
           $this->_redirect($this->adapter->getLoginUrl());
        } 

        // If user is already authenticated and for some reason goes to /auth/cas, 
        // send them to home page.
        $this->_helper->redirector('home','pages');
    }

    public function postCasAction()
    {

       $coopSess = new Zend_Session_Namespace('coop');
       
       /*
        * START SETTING SESSION VARIABLES
        */

       $sem = new My_Model_Semester();
       // Sets the current semester in the database.
       $sem->setCurrentSem();
       
       $db = new My_Db();
       $roles = new Application_Model_DbTable_Role();

       $funcs = new My_Funcs();

       // If user is in the database
       if ( $user = $db->getRow('coop_users', array('username'=>$coopSess->uhinfo['user'])) ) {

          // Set the user's initial session variables along with checking if the student
          // is enrolled.
          $funcs->setSessions($user, $coopSess);

       // If not in the database
       } else {

          // Deny access
          $coopSess->inDb = false;
          $this->_helper->redirector('access-denied', 'pages');

       }

       if ($coopSess->role == 'notActive') {
          $this->_helper->redirector('disclaimer', 'pages');
       }

       if ($coopSess->role == 'user') {
          $login = new My_Model_Logins();
          $login->recordLogin($coopSess->username);
          $this->_helper->redirector('view', 'syllabus');
       }

       $this->_redirect($local_service."/pages/home");
       
    }

    public function logoutAction()
    {
       //if ($this->coopSess->role != 'none') {
       $this->auth->clearIdentity();
       // Specify the landing URL to hit after logout
       $local_service = $this->config_cas->params->local_service;  //get the website URL
       $landingUrl = $this->adapter->getUrl().'/logout?service='.$local_service.'/pages/login';
       $this->adapter->setLogoutUrl($landingUrl);
       Zend_Session::destroy(true);
       //die(var_dump($this->coopSess->role));
       $this->_redirect($this->adapter->getLogoutUrl());
       //$this->render('logout');
       
       //}
    }
}
