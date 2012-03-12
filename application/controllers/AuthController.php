<?php

class AuthController extends Zend_Controller_Action
{

    private $auth = null;

    private $config_cas = null;

    private $adapter = null;

    private $coopSess = null;
    
    
    public function init()
    {
       $this->auth = Zend_Auth::getInstance();
       $this->config_cas = new Zend_Config_Ini(APPLICATION_PATH.'/configs/cas.ini', 'cas');
       $this->adapter = new My_Auth_Adapter_Cas($this->config_cas->params);
       $this->coopSess = new Zend_Session_Namespace('coop');
       
    }

    public function indexAction()
    {
        // action body
    }

    public function casAction()
    {
        // action body
        //$auth = Zend_Auth::getInstance();

        //$config_cas = new Zend_Config_Ini(APPLICATION_PATH.'/configs/cas.ini', 'cas');
        //$adapter = new My_Auth_Adapter_Cas($config_cas->params);
        //$adapter->setService($service); // ASK ABOUT $service. IT WORKS FOR THE SAMPLE APP BUT IS UNDEFINED FOR THIS APP.
        $local_service = $this->config_cas->params->local_service;
        $this->adapter->setLoginUrl();
        
        if(isset($_GET['ticket'])) {
            $this->adapter->setTicket($_GET['ticket']);
            //$this->adapter->setService($service);
            $this->adapter->setQueryParams(array($this->adapter->getTicket(), $this->adapter->getService()));
            //die(var_dump($this->adapter->getQueryParams()));
            //die($this->adapter->getTicket());
        }
        
        if(!$this->auth->hasIdentity() && $this->adapter->hasTicket()) {
            $result = $this->auth->authenticate($this->adapter);
            //die(var_dump($result));
            
            if(!$result->isValid()) {
                //die($result->getMessages());
                $this->view->messages = $result->getMessages();
                return;
            }
            if($this->auth->hasIdentity()) {
               
               $coopSess = new Zend_Session_Namespace('coop');
               $coopSess->uhinfo = $result->getMessages();
               // Assign an initial role of "Guest" to CAS authenticated users.
               // Then overwrite later if they are in the database.
               $coopSess->role = 'guest';
               $persons = new Application_Model_DbTable_Person();
               $person = $persons->getPerson($coopSess->uhinfo['uhuuid']);
               $roles = new Application_Model_DbTable_Role();
               
               
               // If user is in database
               if ($person) {
                  $coopSess->inDb = true;
                  // Get users role
                  $role = $roles->getRole($person['roles_id']);
                  // Make sure user has a role
                  if ($role) {
                     $coopSess->role = $role['role'];
                  }
                  // If user submitted and agreed to initial contract
                  if ($person['agreedto_contract']) {
                     $coopSess->contractStatus = 'contractYes';
                  } else {
                     $coopSess->contractStatus = 'contractNo';
                  }
                  
               } else {
                  $coopSess->inDb = false;
               }
            }
            //die(var_dump($coopSess->uhinfo,$coopSess->contractStatus,$coopSess->role,$coopSess->inDb));
        }

        // Logout if requested
        //if(isset($_GET['logout'])) {

        //    $this->auth->clearIdentity();

        //    // Specify the landing URL to hit after logout
        
        //    $landingUrl = $this->adapter->getUrl().'/logout?service='.$local_service.'/auth/logout';

        //    $this->adapter->setLogoutUrl($landingUrl);
        //    Zend_Session::destroy(true);

        //    $this->_redirect($this->adapter->getLogoutUrl());
        //}

        
        if(!$this->auth->hasIdentity()) {
            // Send to CAS for authentication
            $this->_redirect($this->adapter->getLoginUrl());
        } 
        $coopSess = new Zend_Session_Namespace('coop');
        
        if ($coopSess->inDb) {
           
           if ($coopSess->role != 'user' || $coopSess->contractStatus == 'contractYes') {
              
              $this->_redirect($local_service."/pages/home");
           }
           else {
              $this->_redirect($local_service."/contract/renew");
           }
        }
        $this->_redirect($local_service."/contract/new");
                
    }

    public function loginAction()
    {
        // action body
    }

    public function logoutAction()
    {
       if ($this->coopSess->role != 'none') {
          $this->auth->clearIdentity();
          // Specify the landing URL to hit after logout
          $local_service = $this->config_cas->params->local_service;  //get the website URL
          $landingUrl = $this->adapter->getUrl().'/logout?service='.$local_service.'/auth/logout';
          $this->adapter->setLogoutUrl($landingUrl);
          Zend_Session::destroy(true);
          //die(var_dump($this->coopSess->role));
          $this->_redirect($this->adapter->getLogoutUrl());
          //$this->render('logout');
       }
    }

    
}









