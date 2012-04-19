<?php

class FormController extends Zend_Controller_Action
{

    public function init()
    {
        /* Initialize action controller here */
    }

    public function indexAction()
    {
        // action body
    }

    public function studentInfoShowAction()
    {

       $form = new Application_Form_StudentInfo();
       
       $this->view->form = $form;
       
       if ($this->_request->isPost()) {
          $data = $_POST;
          $valid = $this->handlePost($form, $data);
          if ($valid) {
             $this->_helper->redirector('create');
          }
       }
       

    }

    public function studentInfoSubmitAction()
    {
       $coopSess = new Zend_Session_Namespace('coop');
       if ( isset($coopSess->validData) ) {
          
          $data = $coopSess->validData;
          unset($coopSess->validData);
          
          // create student //
          //$link = My_DbLink::connect();
          $link = new My_Db();
          
          // get only the submited form data that matches table fields in coop_users
          $userVals = $link->prepFormInserts($data, 'coop_users'); 

          // get only the submited form data that matches table fields in coop_users_semesters
          $userSemVals = $link->prepFormInserts($data, 'coop_users_semesters'); 

          // username
          $userVals['username'] = $coopSess->uhinfo['user'];

          // get role for student
          $result = $link->select()->from('coop_roles','id')->where("role = ?", "user");
          $roleId = $link->fetchOne($result);
          $userVals['roles_id'] = $roleId;

          // put dates into proper format for database.
//          $tokens = explode('/',$userVals['grad_date']);
//          $userVals['grad_date'] = $tokens[2] . $tokens[0] . $tokens[1];
//
//          $tokens = explode('/',$userVals['start_date']);
//          $userVals['start_date'] = $tokens[2] . $tokens[0] . $tokens[1];
//
//          $tokens = explode('/',$userVals['end_date']);
//          $userVals['end_date'] = $tokens[2] . $tokens[0] . $tokens[1];

          //die(var_dump($userVals));
          $link->insert('coop_users', $userVals);

          // get id of user just inserted
          $userSemVals['users_id'] = $link->lastInsertId('coop_users');

          $link->insert('coop_users_semesters', $userSemVals);

          $this->_helper->redirector('post-cas', 'auth');
       }
    }



    /* HELPERS */
    
    private function handlePost($form, $data)
    {
       $coopSess = new Zend_Session_Namespace('coop');
       if ($form->isValid($data)) {
          if ($data['agreement'] == 'agree') {
             $coopSess->validData = $data;
             return true;
          } else {
             $this->view->message = 'Must agree before continuing';
             $form->populate($data);
             return false;
          }
       } else {
          return false;
       }
       
    }


}





