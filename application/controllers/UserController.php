<?php

class UserController extends Zend_Controller_Action
{

    public function init()
    {
        /* Initialize action controller here */
    }

    public function indexAction()
    {
        // action body
    }

    public function newAction()
    {
       $coopSess = new Zend_Session_Namespace('coop');
                     
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

    public function createAction()
    {
       /*
        * Requests covered on page 74 of zend book
        */
       
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



    public function updateAction()
    {
        // action body
    }



    public function listUnenrolledAction()
    {
        $coopSess = new Zend_Session_Namespace('coop');

        $link = new My_Db();

        $userId = $coopSess->userId;

        //$select = $link->select()->from(array('u'=>'coop_users'), array('id','fname','lname','username'))
        //                                      
        //                         ->join(array('us'=>'coop_users_semesters'), 'u.id = us.users_id',
        //                                      array('classes_id'))

        //                         ->join(array('c'=>'coop_classes'), 'us.classes_id = c.id',
        //                                      array('class'=>'name'))

        //                         ->where('u.active = 0');

        $select = $link->select()->from(array('u'=>'coop_users'), 
                                        array('users_id'=>'id','fname','lname','username', 
                                              'classes_id', 'semesters_id'))

                                 ->join(array('c'=>'coop_classes'), 'u.classes_id = c.id',
                                        array('class'=>'name'))

                                 ->where('u.active = 0');

        $users = $link->fetchAll($select);
                                 
        //die(var_dump($users));

        $this->view->users = $users;
                
    }

    public function activateAction()
    {
       if ($this->_request->isGet()) {
          $users_id = $this->_request->getQuery('users_id');

          if (isset($users_id)) {

             $link = new My_Db();

             $data = $link->prepFormInserts($_GET, 'coop_users_semesters');

             $link->update('coop_users', array('active'=>1), "id = $users_id");

             $link->insert('coop_users_semesters', $data);

             $this->_helper->redirector('list-unenrolled');

          } else {
             throw new Exception('Must choose a student to enroll.');
          }

       } else {
          throw new Exception('Wrong way of submitting data.');
       }
    }

    public function historySearchAction()
    {
       $form = new Application_Form_HistorySearch();

       $this->view->form = $form;

       if ($this->_request->isPost()) {
          $data = $_POST;

          if ($form->isValid($data)) {
             $coopSess = new Zend_Session_Namespace('coop');

             // Set flag for historyShowAction indicating data is valid
             $coopSess->validData = $data;

             $this->_helper->redirector('history-show');
          }
       }
       

    }

    public function historyShowAction()
    {
       $coopSess = new Zend_Session_Namespace('coop');

       if ( isset($coopSess->validData) )  {

          $data = $coopSess->validData;
          unset($coopSess->validData);

          $username = $data['username'];

          $db = new My_Db();

          $query = $db->select()->from(array('u'=>'coop_users'), array('fname','lname'))
                                ->join(array('us'=>'coop_users_semesters'), 'u.id = us.users_id')
                                ->join(array('c'=>'coop_classes'), 'us.classes_id = c.id',
                                             array('class'=>'name'))
                                ->join(array('s'=>'coop_semesters'), 'us.semesters_id = s.id',
                                             'semester')
                                ->where("u.username = ?", $username)
                                ->order(new Zend_Db_Expr("SUBSTRING_INDEX(semester, ' ', -1) DESC, 
                                                 SUBSTRING_INDEX(semester, ' ', 1) ASC"));
                  

          $history = $db->fetchAll($query);

          //die(var_dump($history));
                                       
          $this->view->history = $history;
          

       } else {
          throw new Exception('Wrong way of submitting data.');
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



