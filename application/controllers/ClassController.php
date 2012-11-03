<?php

class ClassController extends Zend_Controller_Action
{

    public function init()
    {
        /* Initialize action controller here */
    }

    public function changeAction()
    {
       if ($this->_request->isGet()) {
          $coopSess = new Zend_Session_Namespace('coop');
          //die($coopSess->prevAction);
          $id = $this->getRequest()->getParam('classId');

          if (in_array($id, $coopSess->classIds)) {
             $coopSess->currentClassId = $id;
             $class = new My_Model_Class();
             $coopSess->currentClassName = $class->getName($id);
          }

          // If student has incompletes.
          if ( isset($coopSess->incompleteClassIds) ) {

             // If the student is changing to a class which is incomplete, set the current
             // semester id to the incomplete semester id.
             if (in_array($id, $coopSess->incompleteClassIds)) {
                $coopSess->currentSemId = $coopSess->incompleteSemId;
             // Else, set it to the current semester.
             } else {
                $sem = new My_Model_Semester();
                $coopSess->currentSemId = $sem->getCurrentSemId();
             }
          }

          $action = $coopSess->prevAction;
          $controller = $coopSess->prevController;

          // This won't work right if the previous action, controller was one which was
          // meant for async posts
          $this->_helper->redirector($action, $controller);
       
      
       }
    }

    public function listallAction()
    {
       $class = new My_Model_Class();
       $classes = $class->getAll();

       $this->view->classes = $classes;

    }

    public function editAction()
    {
       $form = new Application_Form_ClassEdit();
       $this->view->form = $form;
       if ($this->getRequest()->isGet() && isset($_GET['id'])) {

          $id = $_GET['id'];

          $class = new My_Model_Class();
          $classRec = $class->getClass($id);

          //$data = array('name' => $classRec['name'], 'coordinator' => $classRec['coordinator']);

          //$form = new Application_Form_ClassEdit();

          $form->populate($classRec);

          //$this->view->form = $form;

       } else if ($this->getRequest()->isPost()) {

          $data = $_POST;

          if ($form->isValid($data)) {
             $class = new My_Model_Class();
             $res = $class->edit($data['id'], $data);
             if ($res === 'exists') {
                $this->view->message = "<p class='error'> That class already exists </p>";
             } else {
                $this->_helper->redirector('listall');
             }


          }

       }

    }

    public function deleteAction()
    {
       if ($this->getRequest()->isPost()) {
          $classId = $_POST['classes_id'];

          $class = new My_Model_Class();

          // Delete the class
          // If delete was successfull
          if ($class->delete("id = $classId")) {
             $this->view->success = "<p class='success'> Class has been deleted </p>";
          } else {
             $this->view->success = "<p class='error'> Failed to delete class </p>";
          }

       }

       // Instantiate form after deletion so it will show updated class list
       $form = new Application_Form_ClassDelete();

       $this->view->form = $form;

    }

    public function createAction()
    {

       $form = new Application_Form_AddClass();
       if ($this->getRequest()->isPost()) {
          //$form = new Application_Form_AddClass();
          $data['name'] = $_POST['name'];
          $data['coordinator'] = $_POST['coordinator'];

          if ($form->isValid($data)) {
             $class = new My_Model_Class();
             $res = $class->create($data);
             if ($res === true) {
                $form = new Application_Form_AddClass();
                $this->view->message = "<p class='success'> Class has been added </p>";
             } else if ($res == "exists") {
                $this->view->message = "<p class='error'> Class already exists </p>";
             } else {
                $this->view->message = "<p class='error'> Class could not be added </p>";
             }
          }


       }
       //$form = new Application_Form_AddClass();

       $this->view->form = $form;

    }

    public function listStudentsAction()
    {
       if ($this->getRequest()->isGet()) {
          $req = $this->getRequest();

          $classId = $req->getParam('id'); 

          $message = "";
          if ($req->getParam('success') === 'true') {
             $message = "<p class=success> Student has been dropped </p>";
          } else if ($req->getParam('success') === 'false') {
             $message = "<p class=error> Error occured </p>";
          }
          //die($classId);

          $class = new My_Model_Class();

          $className = $class->getName($classId);
          $roll = $class->getRollForCurrentSem($classId, 'lname');

          $this->view->message = $message;
          $this->view->roll = $roll;
          $this->view->className = $className;

       }

    }

    public function dropStudentAction()
    {
       if ($this->getRequest()->isGet()) {
          $req = $this->getRequest();
          $classId = $req->getParam('classes_id');
          $where['classes_id'] = $req->getParam('classes_id');
          $where['student'] = $req->getParam('student');
          $sem = new My_Model_Semester();
          $where['semesters_id'] = $sem->getCurrentSemId();

          //die(var_dump($where));
          $class = new My_Model_Class();

          if ($class->dropStudent($where)) {
             $success = 'true';
          } else {
             $success = 'false';
          }

          $this->_helper->redirector('list-students', 'class', null, array('id' => $classId, 'success' => $success));

          //die(var_dump($where));
       }

    }

}

