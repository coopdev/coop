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


          $link = new My_Db();

          $data = $link->prepFormInserts($_GET, 'coop_users_semesters');

          $link->update('coop_users', array('active'=>1), "id = $users_id");

          $link->insert('coop_users_semesters', $data);

          $this->_helper->redirector('list-unenrolled');

       }
       die($id);
    }

    public function updateAction()
    {
        // action body
    }


    /* Helpers */
    
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


    /* Tests */

    public function testAction()
    {
       $coopSess = new Zend_Session_Namespace('coop');
       $link = My_DbLink::connect();
       //$config = new Zend_Config_Ini(APPLICATION_PATH.
       //                            '/configs/application.ini','production');
             
       $db = new My_Db();
       //$db = $db->getLink();
       $role = $db->getRowById('coop_roles', 9);
       die(var_dump($role));
       $col = $db->getCol('coop_roles', 'role', array('id'=>9));
       die($col);
       $role = $db->getRow('coop_roles', array('id'=>5));
       die(var_dump($role));
       $id = $db->getId('coop_roles', array('role'=>'user'));
       die(var_dump($id));
       $cols = $db->insertFormData('blah');
       
       $row = $db->fetchRow("SELECT * FROM coop_roles where role = 'user'");
       die(var_dump($cols));

       //$link->update('coop_classes', $updates, 'id = 1');
       
       //$statement = $select->from('coop_users_contracts');
       $statement = $select->from('coop_roles');
       $rows = $link->fetchAll($statement);
       //die(var_dump($rows));
                           
       
//       $paginator = Zend_Paginator::factory($statement);
//       $currentPage = 1;
//       $i = $this->getRequest()->getQuery('i');
//       
//       if (!empty($i)) {
//          $currentPage = $i;
//       }
//       
//       $paginator->setItemCountPerPage(1);
//       $paginator->setPageRange(2);
//       $paginator->setCurrentPageNumber($currentPage);
//       
//       
//       $this->view->paginator = $paginator;
             
       
       /* Testing performance for class instantiation and db queries. */
//       for ($i = 0; $i < 10; $i++) {
//          $link = My_DbLink::connect();
//          $users = $link->fetchAll('SELECT * FROM coop_users');
//          //$a = 'hi';
//       
    }

    public function testformAction()
    {
       $coopSess = new Zend_Session_Namespace('coop');
       $form = new Application_Form_Contract();
       $testForm = new Application_Form_Test();
       $this->view->form = $form;
       
//       if ($this->getRequest()->isPost()) {
//          if ($form->isValid($_POST)) {
//             
//          } else {
//             $this->view->form = $testForm;
//          }
//       
    }

    public function semesterAction()
    {
//      $curDate = date('Y-m-d');
//      $dateParts = explode('-',$curDate);
//      $curYear = $dateParts[0];
//      $curMonth = $dateParts[1];
//      $curSem = '';
//      
//      if ($curMonth < 7) {
//         $curSem = 'Spring';
//      } else {
//         $curSem = 'Fall';
//      }
//      
//      $curSem .= ' ' . $curYear;
//      die($curSem);
       
        $link = My_DbLink::connect();
        $semester = new My_Semester();
        $curSem = $semester->getCurrentSem();
        
        $semPieces = explode(' ',$curSem);
        $curYear = (int)$semPieces[1];
        //$curYear = 2018;
        
        $semesters = $link->fetchRow('SELECT semester FROM coop_semesters');
        
        
                
        $firstSem = $semesters['semester'];
        //die(var_dump($firstSem));
        $firstSem = explode(' ', $firstSem);
        $firstYear = (int)$firstSem[1];
        
        if ($curYear != $firstYear) {
           $link->query('DELETE FROM coop_semesters');
           $query = 'INSERT INTO coop_semesters (semester) VALUES (?)';
           for ($i = $curYear; $i < $curYear+5; $i++) {
              $link->query($query, "Spring $i");
              $link->query($query, "Fall $i");
           }
        }
        
        //test. This can be used to just retrieve a specific range 
        // (e.g. from current semester to 5 years ahead) while keeping more than
        // that range in the database (possibly for checking histories of students).
        // One problem is that the query is returning the results in order (all Fall
        // semesters are returned before Spring semesters), so it mighth take 
        // extra processing to get in proper order to be displayed in select box.
        $yr2 = $curYear+1;
        $yr3 = $curYear+2;
        $yr4 = $curYear+3;
        $yr5 = $curYear+4;
        
        $sems = $link->fetchAll("SELECT semester FROM coop_semesters 
                WHERE semester LIKE '%$curYear%'
                OR semester like '%$yr2%'
                OR semester like '%$yr3%'
                OR semester like '%$yr4%'
                OR semester like '%$yr5%'
                ORDER BY SUBSTRING_INDEX(semester,' ',-1),
                SUBSTRING_INDEX(semester,' ', 1) DESC");
        die(var_dump($sems));
        //end test
        //die(var_dump($firstYear));
        $this->_helper->viewRenderer->setNoRender(true);
    }

   


}

