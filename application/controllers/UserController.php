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


       $form = new Application_Form_NewUser();

       $this->view->form = $form;

       if ($this->_request->isPost()) {
          $data = $_POST;

          if ($form->isValid($data)) {
             $coopSess->validData = $data;

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

       if (isset($coopSess->validData)) {
          $db = new My_Db();

          $data =  $coopSess->validData;
          unset($coopSess->validData);

          $userVals = $db->prepFormInserts($data, 'coop_users');
          $roleId = $db->fetchOne("SELECT id FROM coop_roles WHERE role = 'user'");
          $userVals['roles_id'] = $roleId;

          $usersId = $db->getId('coop_users', array('username'=>$data['username']));


          if (empty($usersId)) {
             $db->insert('coop_users', $userVals);

             $usersId = $db->lastInsertId('coop_users');
          }

          $query = $db->select()->from('coop_users_semesters', 'id')
                          ->where('users_id = ?', $usersId)
                          ->where('semesters_id = ?', $data['semesters_id'])
                          ->where('classes_id = ?', $data['classes_id']);

          $userSemId = $db->fetchOne($query);

          if (!empty($userSemId)) {
             $this->view->message = "That student has already been added for this semester";
          } else {
             $userSemVals = $db->prepFormInserts($data, 'coop_users_semesters');
             $userSemVals['users_id'] = $usersId;
             try {
                $db->insert('coop_users_semesters', $userSemVals);
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

             $username = $data['username'];

             // from historyShow
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
             $this->view->post = true;
                                          
             $this->view->history = $history;

             if (empty($history)) {
                $this->view->message = "No history for that student";
             }
             // from historyShow

             //$this->_helper->redirector('history-show');
          }
       } 
    }

    public function historyShowAction()
    {

       $coopSess = new Zend_Session_Namespace('coop');

       if ( isset($coopSess->validData) )  {

          $data = $coopSess->validData;
          //unset($coopSess->validData);

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
          throw new Exception('Must select a student first');
       }
       
    }

}