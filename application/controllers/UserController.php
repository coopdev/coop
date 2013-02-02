<?php

class UserController extends Zend_Controller_Action
{

    public function init()
    {
        /* Initialize action controller here */
    }


    public function newAction()
    {
       $coopSess = new Zend_Session_Namespace('coop');

       $form = new Application_Form_NewUser();

       $this->view->form = $form;

       if ($this->getRequest()->isPost()) {
           $submitType = $_POST['submitType'];

           $User = new My_Model_User();
           
           if ($submitType === "manual") {
               
               $data = $_POST;
               
               if ($form->isValid($data)) {
                   $result = $User->addStudent($data);
               }

           } else if ($submitType === "file") {

               if (empty($_FILES['fileUpload']['name'])) {
                   $this->view->resultMessage = "<p class=error> No file uploaded </p>";
                   return;
               }
               $data['classes_id'] = $_POST['classes_id'];
               $data['semesters_id'] = $_POST['semesters_id'];
               $data['file'] = $_FILES['fileUpload'];
               
               $result = $User->addStudentsFromFile($data);

           }
           
           if ($result === "noUsername") {
               $this->view->resultMessage = "<p class=error> File must have proper headers. </p>";
           } else if ($result === "enrolled") {
               $this->view->resultMessage = "<p class=error> Student is already enrolled in that class. </p>";
           } else if ($result === true) {
               $this->view->resultMessage = "<p class=success> Success. </p>";
           } else if ($result === false) {
               $this->view->resultMessage = "<p class=error> Error occured. </p>";
           }
       }
    }

    /*
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

    public function createAction()
    {
       
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
             //$studentsId = $db->lastInsertId('coop_students');
          }


          // query to check if student is already enrolled for the specific class and semester.
          $query = $db->select()->from('coop_users_semesters', 'id')
                          ->where('student = ?', $username)
                          ->where('semesters_id = ?', $data['semesters_id'])
                          ->where('classes_id = ?', $data['classes_id']);
                          //->where('coordinator = ?', $data['coordinator']);

          $userSemId = $db->fetchOne($query);

          // if student is enrolled.
          if (!empty($userSemId)) {
             $this->_helper->redirector('new', 'user', null, array('result' => 'fail'));
             //$this->view->result = "That student has already been added for this semester";
          // if student isn't enrolled yet.
          } else {
             $userSemVals = $db->prepFormInserts($data, 'coop_users_semesters');
             $userSemVals['student'] = $username;
             //$userSemVals['students_id'] = $studentsId;
             try {
                $db->insert('coop_users_semesters', $userSemVals);
                $db->insert('coop_students', array('username' => $username, 'semesters_id' => $coopSess->currentSemId));
                $this->_helper->redirector('new', 'user', null, array('result' => 'success'));
                //$this->view->message = "Student has been added";

             } catch (Exception $e) {
                $this->view->message = $e;
             }
          }
       }
       
      
    }
    */

    public function searchstudentAction()
    {
       $form = new Application_Form_StudentRecSearch();

       $this->view->form = $form;

    }

    public function listCoordsAction()
    {
       $user = new My_Model_User();

       //$coords = $user->getCoordInfo(array('username' => 'johndoe'));
       $coords = $user->getCoordInfo();

       $this->view->coords = $coords;
    }

    public function deleteCoordAction()
    {

       if ($this->getRequest()->isPost()) {
          $coord = $_POST['coordinator'];

          $user = new My_Model_User();
          if ($user->deleteCoord($coord)) {
             $this->view->success = true;
          } else {
             $this->view->success = false;
          }
       }

       $form = new Application_Form_DeleteCoord();

       $this->view->form = $form;
    }

    public function addCoordAction()
    {

       $form = new Application_Form_AddCoord();

       $this->view->form = $form;

       if ($this->getRequest()->isPost()) {
          $data = $_POST;
          if ($form->isValid($data)) {
             $user = new My_Model_User();

             $res = $user->addCoord($data);

             if ($res === true) {
                $this->view->success = true;
             } else if ($res == 'exists') {
                $this->view->success = "exists";
             } else {
                $this->view->success = false;
             }
          }
       }

    }

    public function editCoordAction()
    {
       // Use AddCord form since it uses same fields
       $form = new Application_Form_AddCoord();
       $hidden = new Zend_Form_Element_Hidden('origUsername');
       $form->addElement($hidden);
       $form->getElement('Add')->setLabel('Update');

       if ($this->getRequest()->isGet()) {
          $username = $_GET['username'];
          $user = new My_Model_User();
          $coords = $user->getCoordInfo(array('username' => $username));
          //die(var_dump($coords));
          if (empty($coord)) {
             $coord = $coords[0];
          }
          $coord['origUsername'] = $username;
          //die(var_dump($coords));

          //$coords['']

          $form->populate($coord);


       } else if ($this->getRequest()->isPost()) {

          $data = $_POST;

          if ($form->isValid($data)) {
             $user = new My_Model_User();

             $res = $user->editCoord($data['origUsername'], $data);
             if ($res === false) {
                $this->view->message = "<p class='error'> Unable to update coordinator </p>";
             } else {
                $this->_helper->redirector('list-coords');
             }
          }

       }
       //$form->populate(array('username' =>'test'));

       $this->view->form = $form;
    }

    public function listStudentaidsAction()
    {
       $user = new My_Model_User();

       $this->view->stuAids = $user->getStuAidInfo();

    }

    public function addStudentAidAction()
    {
       $form = new Application_Form_AddStudentAid();

       $this->view->form = $form;

       if ($this->getRequest()->isPost()) {
          $data = $_POST;

          if ($form->isValid($data)) {
             $user = new My_Model_User();

             $res = $user->addStudentAid($data);

             if ($res === 'exists') {
                $this->view->message = "<p class=error> Student aid already exists </p>";
             } else if ($res === false) {
                $this->view->message = "<p class=error> Failed to add student aid </p>";
             } else {
                $this->view->message = "<p class=success> Student aid has been added </p>";
             }
          }
       }

    }


    public function editStudentAidAction()
    {
       // Use AddStudentAid form since it uses same fields
       $form = new Application_Form_AddStudentAid();
       $hidden = new Zend_Form_Element_Hidden('origUsername');
       $form->addElement($hidden);

       if ($this->getRequest()->isGet()) {
          $username = $_GET['username'];
          $user = new My_Model_User();
          $stuAids = $user->getStuAidInfo(array('username' => $username));
          //die(var_dump($stuAid));
          if (empty($stuAid)) {
             $stuAid = $stuAids[0];
          }
          $stuAid['origUsername'] = $username;
          //die(var_dump($coords));

          //$coords['']

          $form->populate($stuAid);


       } else if ($this->getRequest()->isPost()) {

          $data = $_POST;

          if ($form->isValid($data)) {
             $user = new My_Model_User();

             $res = $user->editStuAid($data['origUsername'], $data);
             if ($res === false) {
                $this->view->message = "<p class='error'> Unable to update coordinator </p>";
             } else {
                $this->_helper->redirector('list-studentaids');
             }
          }

       }
       //$form->populate(array('username' =>'test'));

       $this->view->form = $form;
    }


    public function deleteStudentAidAction()
    {

       if ($this->getRequest()->isPost()) {
          $stuAid = $_POST['studentAid'];

          $user = new My_Model_User();
          if ($user->deleteStuAid($stuAid)) {
             $this->view->success = true;
          } else {
             $this->view->success = false;
          }
       }

       $form = new Application_Form_DeleteStuAid();

       $this->view->form = $form;
    }

    public function viewLoginsAction()
    {
       $form = new Application_Form_ViewLogins();

       $this->view->form = $form;
    }

    public function viewExtendedDuedatesAction()
    {
        $Assignment = new My_Model_Assignment();
        $this->view->extDuedates = $Assignment->getExtendedDuedates(array("cur_sem" => 1));

    }

    public function deleteExtendedDuedateAction()
    {
        if ($this->getRequest()->isGet()) {
            $id = $this->getRequest()->getParam('id');

            $Assignment = new My_Model_Assignment();

            $Assignment->deleteExtendedDuedate($id);

            $this->_helper->redirector("view-extended-duedates");
        }

    }
}

