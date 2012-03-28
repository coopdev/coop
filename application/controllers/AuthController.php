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
           
           // Validates ticket
            $result = $this->auth->authenticate($this->adapter);
            
            // If successfully validated ticket
            if(!$result->isValid()) {
                //die($result->getMessages());
                $this->view->messages = $result->getMessages();
                return;
            }
            if($this->auth->hasIdentity()) {
               
               /*
                * Check if the current semester is in the contracts table
                * to see if a new contract needs to be filled out.
                */
               $link = My_DbLink::connect();
               $curSemester = new My_Semester();
               
               // Get currest semester.
               $curSemester = $curSemester->getCurrentSem();
               
               // Checks if there is an agreement form for current semester in
               // coop_contracts table.
               $qryResult = $link->query("SELECT id FROM coop_contracts WHERE
                                       semester = '$curSemester'");
               $record = $qryResult->fetch();
               $contractId = $record['id'];
               
               // If current semester isn't in contracts table, set all users
               // "agreedto_contract" fields to 0 and insert the current semester
               // into the contracts table.
               if (!$contractId) {
                  //die('hi');
                  $link->query("UPDATE coop_users SET agreedto_contract = 0");
                  $link->insert('coop_contracts', array('semester'=>$curSemester));

               }
                              
               $coopSess = new Zend_Session_Namespace('coop');
               
               // Get messages returned from CAS server.
               $coopSess->uhinfo = $result->getMessages();
               
               // Assign an initial role of "Guest" to CAS authenticated users.
               // Then overwrite later if they are in the database.
               $coopSess->role = 'guest';
               $users = new Application_Model_DbTable_User();
               $user = $users->getUser($coopSess->uhinfo['uhuuid']);
               $roles = new Application_Model_DbTable_Role();
               
               
               // If user is in database
               if ($user) {
                  $coopSess->inDb = true;
                  // Get users role
                  $role = $roles->getRole($user['roles_id']);
                  // Make sure user has a role
                  if ($role) {
                     $coopSess->role = $role['role'];
                  }
                  // If user submitted and agreed to initial contract
                  if ($user['agreedto_contract']) {
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
        
        // If user is in database.
        if ($coopSess->inDb) {
           // Non student users or users who have submitted the agreement form
           // can go straighth to home page.
           if ($coopSess->role != 'normal' || $coopSess->contractStatus == 'contractYes') {
              
              $this->_redirect($local_service."/pages/home");
           }
           else {
              $this->_redirect($local_service."/contract/renew");
           }
        }
        // Users not in database get sent to agreement form.
        $this->_redirect($local_service."/contract/new");
                
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









