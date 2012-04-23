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

    public function indexAction()
    {
        // action body
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

//       // Get currest semester.
//       $curSemester = new My_Semester();
//       $curSemester = $curSemester->getCurrentSem();
//       
//       $db = new My_Db();
//
//       // Get the semester that the database thinks is the current semester.
//       $sem = $db->getCol('coop_semesters', 'semester', array('current'=>1));
//
//       // If the current semester is not set as current in the database, it means the semester 
//       // has changed and the database needs to update the current semester.
//       if ($curSemester != $sem) {
//          $db->update('coop_semesters', array('current'=>0), 'current = 1');
//
//          $db->update('coop_semesters', array('current'=>1), "semester = '$curSemester'");
//
//          // Because it is a new semester, deactivate users so they have to accept the 
//          // disclaimer again.
//          $db->update('coop_users', array('active'=>0));
//       }
       
       
       // Checks if there is an agreement form for current semester in
       // coop_contracts table.
//       $qryResult = $link->query("SELECT id FROM coop_contracts WHERE
//                               semester = '$curSemester'");
//       $record = $qryResult->fetch();
//       $contractId = $record['id'];
       
       // If current semester isn't in contracts table, set all users
       // "agreedto_contract" fields to 0 and insert the current semester
       // into the contracts table.
//       if (!$contractId) {
//          //die('hi');
//          $link->query("UPDATE coop_users SET agreedto_contract = 0");
//          $link->insert('coop_contracts', array('semester'=>$curSemester));
//
//       }
       
       /*
        * START SETTING SESSION VARIABLES
        */

       $sem = new My_Semester();
       $sem->setCurrentSem();
       
       $db = new My_Db();
       $roles = new Application_Model_DbTable_Role();

       $funcs = new My_Funcs();

       // If user is in coop_users (student)
       if ( $user = $db->getRow('coop_users', array('username'=>$coopSess->uhinfo['user'])) ) {

          $coopSess->role = 'user';

          $funcs->setSessions($user, $coopSess);
       
          // If user is in coop_coordinators (coordinator)
       } else if ( $user = $db->getRow('coop_coordinators', array('username'=>$coopSess->uhinfo['user'])) ) {

          $coopSess->role = 'coordinator';

          $funcs->setSessions($user, $coopSess);

       } else {

          $coopSess->inDb = false;
          $this->_helper->redirector('access-denied', 'pages');

       }

       if ($coopSess->role == 'notActive') {
          $this->_helper->redirector('disclaimer', 'pages');
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

    public function dbAction()
    {

        
    }


}