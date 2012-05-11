<?php

class UserController extends Zend_Controller_Action
{

    public function init()
    {
        /* Initialize action controller here */
    }

    // Shows the Add Student page
    public function newAction()
    {
       $coopSess = new Zend_Session_Namespace('coop');


       $form = new Application_Form_NewUser();

       $this->view->form = $form;

       $params = $this->getRequest()->getParams();
       if (isset($params['result'])) {
          $this->getRequest()->setParam('result', null);
          $this->getRequest()->clearParams();
          if ($params['result'] == 'success') {
             $this->view->message = "<p class='success'> Student has been added </p>";
          } else if ($params['result'] == 'fail') {
             $this->view->message = "<p class='error'> That student has already been added </p>";
          }
          //$this->getRequest()->setParam('result', null);
       }

       if ($this->_request->isPost()) {
          $data = $_POST;

          if ($form->isValid($data)) {
             $coopSess->validData = $data;

             $this->_helper->redirector('create');
          }
       } 


    }

    // Inserts a student
    public function createAction()
    {
       /*
        * Requests covered on page 74 of zend book
        */
       
       $coopSess = new Zend_Session_Namespace('coop');

       if (isset($coopSess->validData)) {
          $db = new My_Db();

          $data =  $coopSess->validData;
          unset($coopSess->validData);

          $userVals = $db->prepFormInserts($data, 'coop_users');
          $username = $userVals['username'];
          $roleId = $db->fetchOne("SELECT id FROM coop_roles WHERE role = 'user'");
          $userVals['roles_id'] = $roleId;

          $usersId = $db->getId('coop_users', array('username'=>$username));
          //$studentsId = $db->getId('coop_students', array('users_id'=>$usersId));


          // If user does not already exists
          if (empty($usersId)) {
             $db->insert('coop_users', $userVals);
             //$usersId = $db->lastInsertId('coop_users');
             $db->insert('coop_students', array('username' => $username));
             //$studentsId = $db->lastInsertId('coop_students');
          }


          $query = $db->select()->from('coop_users_semesters', 'id')
                          ->where('student = ?', $username)
                          ->where('semesters_id = ?', $data['semesters_id'])
                          ->where('classes_id = ?', $data['classes_id']);

          $userSemId = $db->fetchOne($query);

          if (!empty($userSemId)) {
             $this->_helper->redirector('new', 'user', null, array('result' => 'fail'));
             $this->view->result = "That student has already been added for this semester";
          } else {
             $userSemVals = $db->prepFormInserts($data, 'coop_users_semesters');
             $userSemVals['student'] = $username;
             //$userSemVals['students_id'] = $studentsId;
             try {
                $db->insert('coop_users_semesters', $userSemVals);
                $this->_helper->redirector('new', 'user', null, array('result' => 'success'));
                $this->view->message = "Student has been added";

             } catch (Exception $e) {
                $this->view->message = $e;
             }
          }
       }
       
      
    }

    public function updateAction()
    {
        // action body
    }




    public function searchstudentAction()
    {
       $form = new Application_Form_StudentRecSearch();

       $this->view->form = $form;

//       if ($this->_request->isPost() || $this->_request->isGet()) {
//
//          $data = $_POST;
//
//          if ($form->isValid($data)) {
//             $coopSess = new Zend_Session_Namespace('coop');
//
//             $username = $data['username'];
//
//             // from historyShow
//             $db = new My_Db();
//
//             $query = $db->select()->from(array('u'=>'coop_users'), array('fname','lname'))
//                                   ->join(array('us'=>'coop_users_semesters'), 'u.username = us.student')
//                                   ->join(array('c'=>'coop_classes'), 'us.classes_id = c.id',
//                                                array('class'=>'name'))
//                                   ->join(array('s'=>'coop_semesters'), 'us.semesters_id = s.id',
//                                                'semester')
//                                   ->where("u.username = ?", $username)
//                                   ->order(new Zend_Db_Expr("SUBSTRING_INDEX(semester, ' ', -1) DESC, 
//                                                    SUBSTRING_INDEX(semester, ' ', 1) ASC"));
//
//             $history = $db->fetchAll($query);
//
//             //die(var_dump($history));
//             $this->view->post = true;
//                                          
//             $this->view->history = $history;
//
//             if (empty($history)) {
//                $this->view->message = "No history for that student";
//             }
//             // from historyShow
//
//             //$this->_helper->redirector('history-show');
//          }
//       } 
    }

}