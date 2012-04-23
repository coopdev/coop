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

    public function loginAction()
    {
       $form = new Application_Form_Login();

       $this->view->form = $form;

       if ($this->_request->isPost()) {
          $data = $_POST;

          if ($form->isValid($data)) {
             $username = $data['username'];
             $password = $data['password'];

             $sem = new My_Semester();
             $sem->setCurrentSem();

             $db = new My_Db();
             $coopSess = new Zend_Session_Namespace('coop');
             $funcs = new My_Funcs();

             if ($user = $db->getRow('coop_users', array('username'=>$username))) {
                if ($user['password'] == $password) {
                   $coopSess->role = 'user';

                   $funcs->setSessions($user, $coopSess);

                   if ($coopSess->role == 'notActive') {
                      $this->_helper->redirector('disclaimer', 'pages');
                   }

                   $this->_helper->redirector('home');
                   
                } 
             } else if ( $user = $db->getRow('coop_coordinators', array('username'=>$username))) {
                if ($user['password'] == $password) {
                   $coopSess->role = 'coordinator';

                   $funcs->setSessions($user, $coopSess);

                   $this->_helper->redirector('home');
                }
             } else if ( $user = $db->getRow('coop_supervisors', array('username'=>$username))) {
                if ($user['password'] == $password) {
                   $coopSess->role = 'supervisor';

                   $funcs->setSessions($user, $coopSess);

                   $this->_helper->redirector('home');
                }
             }

             $this->view->message = "Incorrect username or password";
          }
       
       }
    }

    public function homeAction()
    {
        $coopSess = new Zend_Session_Namespace('coop');
        
        $this->view->uhinfo = $coopSess->uhinfo;
        $this->view->role = $coopSess->role;
        /**
         *  Bottom three lines used as a test: first gets current instance of authenticated user.
         *  Next gets the stored information of the current authenticated user 
         *  (in this case "uhinfo" because that was written to storage during CAS authentication.)
         */
        //$auth = Zend_Auth::getInstance();
        //$identity = $auth->getStorage()->read();
        //die(var_dump($identity));
    }

    public function studentsAction()
    {
        // action body
    }

    public function teachersAction()
    {
        // action body
    }

    public function disclaimerAction()
    {
       $form = new Application_Form_Disclaimer();

       $this->view->form = $form;

       if ($this->_request->isPost()) {

          $data = $_POST;

          if($form->isValid($data)) {
             
             if (isset($data['agreement']) && $data['agreement'] == true) {
                $coopSess = new Zend_Session_Namespace('coop');

                $coopSess->role = 'user';

                $db = new My_Db();
                $db->update('coop_users', array('active'=>1), "id = ".$coopSess->userId);

                $semId = $db->getId('coop_semesters', array('current'=>1));

                $vals = array('users_id'=> $coopSess->userId, 'semesters_id' => $semId, 'date_agreed'=> date('Ymd') );
                $db->insert('coop_disclaimers', $vals);

                $this->_helper->redirector('home');

             } else {
                 
                $this->view->message = "Must agree before continuing";
             }

          }
       }


       
    }

    public function accessDeniedAction()
    {
        
    }


}











