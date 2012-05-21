<?php

class ClassController extends Zend_Controller_Action
{

    public function init()
    {
        /* Initialize action controller here */
    }

    public function changeAction()
    {
       if ($this->getRequest()->isGet()) {
          $coopSess = new Zend_Session_Namespace('coop');
          $id = $this->getRequest()->getParam('classId');

          if (in_array($id, $coopSess->classIds)) {
             $coopSess->currentClassId = $id;
             $class = new My_Model_Class();
             $coopSess->currentClassName = $class->getName($id);
          }

          $action = $coopSess->prevAction;
          $controller = $coopSess->prevController;
          $url = $coopSess->prevUrl;

          //die($coopSess->currentClassId);

          // This won't work right if the previous action, controller was one which was
          // meant for async posts
          //die("$controller: $action");
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


}

